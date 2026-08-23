<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Diagnostics;

use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use GreatMarketrealmCompanion\Modules\Administration\Settings\CompanionSettings;

defined('ABSPATH') || exit;

final class StewardDiagnostics
{
    public function __construct(
        private GateSecuritySettings $gateSecurity,
        private CompanionSettings $settings
    ) {
    }

    /**
     * @return array{
     *   checks:array<int,array{key:string,label:string,status:string,detail:string}>,
     *   counts:array{healthy:int,attention:int,info:int},
     *   seal:string,
     *   environment:array<string,string>
     * }
     */
    public function report(): array
    {
        $checks = [
            $this->phpCheck(),
            $this->wordpressCheck(),
            $this->httpsCheck(),
            $this->uploadsCheck(),
            $this->httpCheck(),
            $this->turnstileCheck(),
            $this->registrationCheck(),
            $this->loginCheck(),
        ];

        $counts = ['healthy' => 0, 'attention' => 0, 'info' => 0];
        foreach ($checks as $check) {
            $counts[$check['status']]++;
        }

        return [
            'checks' => $checks,
            'counts' => $counts,
            'seal' => $counts['attention'] === 0
                ? 'The Companion is in good order.'
                : 'The Companion would benefit from the Steward\'s attention.',
            'environment' => $this->environment(),
        ];
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function phpCheck(): array
    {
        $healthy = version_compare(PHP_VERSION, '8.1.0', '>=');
        return $this->check(
            'php',
            'PHP runtime',
            $healthy ? 'healthy' : 'attention',
            $healthy ? 'PHP ' . PHP_VERSION . ' meets the Companion baseline.' : 'PHP 8.1 or newer is required.'
        );
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function wordpressCheck(): array
    {
        global $wp_version;
        $version = isset($wp_version) ? (string) $wp_version : 'Unknown';
        return $this->check('wordpress', 'WordPress runtime', 'healthy', 'WordPress ' . $version . ' is active.');
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function httpsCheck(): array
    {
        $secure = function_exists('is_ssl') && is_ssl();
        return $this->check(
            'https',
            'HTTPS',
            $secure ? 'healthy' : 'attention',
            $secure ? 'The current Steward request is protected by HTTPS.' : 'The current request is not using HTTPS.'
        );
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function uploadsCheck(): array
    {
        $upload = wp_upload_dir(null, false);
        $error = (string) ($upload['error'] ?? '');
        $basedir = (string) ($upload['basedir'] ?? '');
        $healthy = $error === '' && $basedir !== '' && is_dir($basedir) && is_writable($basedir);

        return $this->check(
            'uploads',
            'Media uploads',
            $healthy ? 'healthy' : 'attention',
            $healthy ? 'The WordPress uploads directory is writable.' : ($error !== '' ? $error : 'The uploads directory is not writable.')
        );
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function httpCheck(): array
    {
        $healthy = function_exists('wp_remote_post');
        return $this->check(
            'http',
            'Outbound HTTP',
            $healthy ? 'healthy' : 'attention',
            $healthy ? 'WordPress HTTP services are available for Turnstile and future integrations.' : 'WordPress HTTP services are unavailable.'
        );
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function turnstileCheck(): array
    {
        return $this->check(
            'turnstile',
            'Cloudflare Turnstile',
            $this->gateSecurity->configured() ? 'healthy' : 'attention',
            $this->gateSecurity->configured() ? 'Site and Secret keys are configured.' : 'Turnstile credentials have not been completed.'
        );
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function registrationCheck(): array
    {
        return $this->check(
            'registration',
            'Registration protection',
            $this->gateSecurity->protects('registration') ? 'healthy' : 'info',
            $this->gateSecurity->protects('registration') ? 'New Guild registrations are protected by Turnstile.' : 'Registration protection is currently disabled.'
        );
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function loginCheck(): array
    {
        return $this->check(
            'login',
            'Login protection',
            $this->gateSecurity->protects('login') ? 'healthy' : 'info',
            $this->gateSecurity->protects('login') ? 'Guild member login is protected by Turnstile.' : 'Login protection is currently disabled.'
        );
    }

    /** @return array<string,string> */
    private function environment(): array
    {
        global $wp_version;
        $preferences = $this->settings->all();
        if (! $preferences['show_environment_details']) {
            return [];
        }

        return [
            'Companion version' => defined('GMRC_VERSION') ? (string) GMRC_VERSION : 'Unknown',
            'WordPress version' => isset($wp_version) ? (string) $wp_version : 'Unknown',
            'PHP version' => PHP_VERSION,
            'Site URL' => home_url('/'),
            'WordPress memory limit' => defined('WP_MEMORY_LIMIT') ? (string) WP_MEMORY_LIMIT : 'WordPress default',
        ];
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function check(string $key, string $label, string $status, string $detail): array
    {
        return compact('key', 'label', 'status', 'detail');
    }
}
