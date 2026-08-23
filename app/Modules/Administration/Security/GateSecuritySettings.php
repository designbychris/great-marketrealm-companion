<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Security;

defined('ABSPATH') || exit;

final class GateSecuritySettings
{
    public const OPTION_NAME = 'gmrc_gate_security';
    public const PROVIDER = 'turnstile';

    /** @return array{provider:string,site_key:string,secret_key:string,protect_registration:bool,protect_login:bool} */
    public function all(): array
    {
        $stored = get_option(self::OPTION_NAME, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'provider' => self::PROVIDER,
            'site_key' => (string) ($stored['site_key'] ?? ''),
            'secret_key' => (string) ($stored['secret_key'] ?? ''),
            'protect_registration' => ! empty($stored['protect_registration']),
            'protect_login' => ! empty($stored['protect_login']),
        ];
    }

    public function configured(): bool
    {
        $settings = $this->all();
        return $settings['site_key'] !== '' && $settings['secret_key'] !== '';
    }

    public function protects(string $intent): bool
    {
        $settings = $this->all();
        $key = $intent === 'login' ? 'protect_login' : 'protect_registration';
        return $this->configured() && $settings[$key];
    }

    public function save(string $siteKey, string $secretKey, bool $registration, bool $login): void
    {
        $current = $this->all();
        $secret = trim($secretKey) !== '' ? trim($secretKey) : $current['secret_key'];

        update_option(self::OPTION_NAME, [
            'provider' => self::PROVIDER,
            'site_key' => trim($siteKey),
            'secret_key' => $secret,
            'protect_registration' => $registration,
            'protect_login' => $login,
        ], false);
    }

    public function clearSecret(): void
    {
        $settings = $this->all();
        $settings['secret_key'] = '';
        update_option(self::OPTION_NAME, $settings, false);
    }
}
