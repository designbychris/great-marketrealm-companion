<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\AuthenticateGuildMember;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildPortraitManager;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\RegisterGuildMember;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\UpdateGuildProfile;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\TurnstileVerifier;
use Throwable;

defined('ABSPATH') || exit;

final class GuildGateController
{
    public function __construct(
        private ViewFactory $views,
        private Request $request,
        private Router $router,
        private ResponseFactory $responses,
        private FlashStore $flash,
        private AuthenticateGuildMember $authenticate,
        private RegisterGuildMember $registerMember,
        private UpdateGuildProfile $updateProfile,
        private GuildPortraitManager $portraits,
        private GateSecuritySettings $gateSecurity,
        private TurnstileVerifier $turnstile
    ) {
    }

    public function show(): string
    {
        return $this->views->render(
            View::make('guildgate.index', [
                'returnRoute' => $this->returnRoute(),
                'accountTypes' => AccountType::values(),
                'turnstile' => $this->gateSecurity->all(),
                'turnstileConfigured' => $this->gateSecurity->configured(),
            ])
        );
    }

    public function profile(): string
    {
        $user = wp_get_current_user();
        $accountType = GuildProfile::accountType((int) $user->ID);
        $portraitId = GuildProfile::portraitAttachmentId((int) $user->ID);

        return $this->views->render(
            View::make('guildgate.profile', [
                'guildUser' => $user,
                'accountType' => $accountType,
                'accountTypeLabel' => AccountType::label($accountType),
                'portraitId' => $portraitId,
                'profileBio' => GuildProfile::bio((int) $user->ID),
                'passwordUrl' => wp_lostpassword_url($this->profileUrl()),
                'logoutUrl' => wp_logout_url($this->gateUrl()),
            ])
        );
    }

    public function updateProfile(): RedirectResponse
    {
        $userId = get_current_user_id();

        try {
            $this->updateProfile->handle(
                $userId,
                $this->request->string('display_name'),
                $this->request->string('email'),
                $this->request->string('profile_bio')
            );
            $this->flash->success('Your Guild profile has been updated.');
        } catch (Throwable $exception) {
            $this->flash->flashOldInput([
                'display_name' => $this->request->string('display_name'),
                'email' => $this->request->string('email'),
                'profile_bio' => $this->request->string('profile_bio'),
            ]);
            $this->flash->error($exception->getMessage());
        }

        return $this->responses->redirect($this->profileUrl());
    }

    public function uploadPortrait(): RedirectResponse
    {
        try {
            $file = isset($_FILES['gmrc_profile_portrait'])
                && is_array($_FILES['gmrc_profile_portrait'])
                    ? $_FILES['gmrc_profile_portrait']
                    : [];

            $this->portraits->upload(get_current_user_id(), $file);
            $this->flash->success('The Guild Illuminator has framed your new profile portrait.');
        } catch (Throwable $exception) {
            $this->flash->error($exception->getMessage());
        }

        return $this->responses->redirect($this->profileUrl());
    }

    public function removePortrait(): RedirectResponse
    {
        $this->portraits->remove(get_current_user_id());
        $this->flash->success('Your custom portrait has been removed. The Guild avatar has been restored.');

        return $this->responses->redirect($this->profileUrl());
    }

    public function login(): RedirectResponse
    {
        if (is_user_logged_in()) {
            return $this->responses->redirect($this->returnUrl());
        }

        $login = $this->request->string('login');
        $password = (string) $this->request->input('password', '');
        $remember = $this->request->string('remember') === '1';

        try {
            $this->verifyGateSecurity('login');
            $this->authenticate->handle($login, $password, $remember);
            $this->flash->success('Welcome back. The Guild Gate is open.');

            return $this->responses->redirect($this->returnUrl());
        } catch (Throwable $exception) {
            $this->flash->flashOldInput([
                'gate_intent' => 'login',
                'login' => $login,
                'return_route' => $this->returnRoute(),
            ]);
            $this->flash->error($exception->getMessage());

            return $this->responses->redirect($this->gateUrl());
        }
    }

    public function register(): RedirectResponse
    {
        if (is_user_logged_in()) {
            return $this->responses->redirect($this->returnUrl());
        }

        $input = [
            'display_name' => $this->request->string('display_name'),
            'username' => $this->request->string('username'),
            'email' => $this->request->string('email'),
            'account_type' => $this->request->string('account_type'),
            'return_route' => $this->returnRoute(),
        ];

        try {
            $this->verifyGateSecurity('register');
            $userId = $this->registerMember->handle(
                $input['username'],
                $input['email'],
                (string) $this->request->input('password', ''),
                $input['display_name'],
                $input['account_type']
            );

            wp_set_current_user($userId);
            wp_set_auth_cookie($userId, true, is_ssl());
            $this->flash->success('Your Guild papers are sealed. Welcome to the Companion.');

            return $this->responses->redirect($this->returnUrl());
        } catch (Throwable $exception) {
            $this->flash->flashOldInput(['gate_intent' => 'register'] + $input);
            $this->flash->error($exception->getMessage());

            return $this->responses->redirect($this->gateUrl());
        }
    }

    private function verifyGateSecurity(string $intent): void
    {
        if (! $this->gateSecurity->protects($intent)) {
            return;
        }

        $token = $this->request->string('cf-turnstile-response');
        $remoteIp = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
            ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']))
            : '';
        $this->turnstile->verify($token, $remoteIp);
    }

    private function returnRoute(): string
    {
        $route = trim(
            $this->request->string(
                'return_route',
                $this->request->string(
                    'gmrc_route',
                    (string) $this->flash->old('return_route', '')
                )
            ),
            '/'
        );

        return $route !== '' && $this->router->has('GET', '/' . $route)
            ? $route
            : 'dashboard';
    }

    private function returnUrl(): string
    {
        $route = $this->returnRoute();
        $url = $this->gateUrl();

        return $route === 'dashboard'
            ? $url
            : add_query_arg('gmrc_route', $route, $url);
    }

    private function profileUrl(): string
    {
        return add_query_arg('gmrc_route', 'guild-profile', $this->gateUrl());
    }

    private function gateUrl(): string
    {
        return home_url('/companion/');
    }
}
