<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined('ABSPATH') || exit;

/**
 * Phase III.M.6B — secure native Guild Gate handoff.
 *
 * Native clients never receive WordPress passwords/cookies. The system browser
 * completes the existing Guild Gate login, a short-lived PKCE code returns to
 * the app, and the code is exchanged once for an opaque bearer token.
 */
final class PocketNativeAuth
{
    public const CALLBACK_URI = 'uk.co.greatmarketrealm.pocket://auth/callback';
    private const REQUEST_TTL = 600;
    private const CODE_TTL = 120;
    private const TOKEN_TTL = 2592000; // 30 days.

    public static function register(): void
    {
        add_filter('determine_current_user', [self::class, 'authenticateBearer'], 25);
    }

    public static function registerRoutes(): void
    {
        register_rest_route('gmrc-pocket/v1', '/native/begin', [
            'methods' => 'POST',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'begin'],
        ]);

        register_rest_route('gmrc-pocket/v1', '/native/token', [
            'methods' => 'POST',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'exchange'],
        ]);

        register_rest_route('gmrc-pocket/v1', '/native/revoke', [
            'methods' => 'POST',
            'permission_callback' => static fn (): bool => is_user_logged_in() && get_current_user_id() > 0,
            'callback' => [self::class, 'revoke'],
        ]);
    }

    public static function begin(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $body = $request->get_json_params();
        if (! is_array($body)) {
            return self::error('gmrc_native_begin_body', 'A JSON body is required.', 400);
        }

        $challenge = isset($body['code_challenge']) && is_string($body['code_challenge'])
            ? trim($body['code_challenge']) : '';
        $state = isset($body['state']) && is_string($body['state']) ? trim($body['state']) : '';

        if (! preg_match('/^[A-Za-z0-9_-]{43,128}$/', $challenge)) {
            return self::error('gmrc_native_challenge', 'A valid PKCE S256 challenge is required.', 400);
        }
        if (! preg_match('/^[A-Za-z0-9._~-]{16,128}$/', $state)) {
            return self::error('gmrc_native_state', 'A valid native state value is required.', 400);
        }

        $requestId = bin2hex(random_bytes(24));
        set_transient(self::requestKey($requestId), [
            'challenge' => $challenge,
            'state' => $state,
            'created_at' => time(),
        ], self::REQUEST_TTL);

        $authorizeUrl = self::browserCompletionUrl($requestId);

        return self::noStore([
            'authorize_url' => esc_url_raw($authorizeUrl),
            'callback_uri' => self::CALLBACK_URI,
            'code_challenge_method' => 'S256',
            'expires_in' => self::REQUEST_TTL,
        ]);
    }

    /** Called after the existing Guild Gate has authenticated the browser user. */
    public static function completeBrowserHandoff(string $requestId): string|WP_Error
    {
        if (! is_user_logged_in() || get_current_user_id() <= 0) {
            return self::error('gmrc_native_login_required', 'Guild Gate authentication is required.', 401);
        }
        if (! preg_match('/^[a-f0-9]{48}$/', $requestId)) {
            return self::error('gmrc_native_request', 'The native sign-in request is invalid.', 400);
        }

        $pending = get_transient(self::requestKey($requestId));
        delete_transient(self::requestKey($requestId));
        if (! is_array($pending) || ! isset($pending['challenge'], $pending['state'])) {
            return self::error('gmrc_native_request_expired', 'The native sign-in request has expired. Return to the app and try again.', 410);
        }

        $code = self::randomToken();
        set_transient(self::codeKey($code), [
            'user_id' => get_current_user_id(),
            'challenge' => (string) $pending['challenge'],
        ], self::CODE_TTL);

        return self::CALLBACK_URI . '?' . http_build_query([
            'code' => $code,
            'state' => (string) $pending['state'],
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public static function exchange(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $body = $request->get_json_params();
        $code = is_array($body) && isset($body['code']) && is_string($body['code']) ? trim($body['code']) : '';
        $verifier = is_array($body) && isset($body['code_verifier']) && is_string($body['code_verifier']) ? trim($body['code_verifier']) : '';

        if (! preg_match('/^[A-Za-z0-9_-]{43}$/', $code) || ! preg_match('/^[A-Za-z0-9._~-]{43,128}$/', $verifier)) {
            return self::error('gmrc_native_exchange', 'A valid authorization code and PKCE verifier are required.', 400);
        }

        $pending = get_transient(self::codeKey($code));
        delete_transient(self::codeKey($code)); // Authorization codes are single use, even on a failed verifier.
        if (! is_array($pending) || ! isset($pending['user_id'], $pending['challenge'])) {
            return self::error('gmrc_native_code_expired', 'The authorization code is invalid or has expired.', 400);
        }

        $actual = self::pkceChallenge($verifier);
        if (! hash_equals((string) $pending['challenge'], $actual)) {
            return self::error('gmrc_native_pkce', 'The native sign-in proof could not be verified.', 403);
        }

        $userId = (int) $pending['user_id'];
        if ($userId <= 0 || get_userdata($userId) === false) {
            return self::error('gmrc_native_user', 'The Guild member could not be found.', 401);
        }

        $token = self::randomToken();
        set_transient(self::tokenKey($token), [
            'user_id' => $userId,
            'expires_at' => time() + self::TOKEN_TTL,
        ], self::TOKEN_TTL);

        return self::noStore([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => self::TOKEN_TTL,
            'scope' => 'gmrc-pocket',
        ]);
    }

    public static function revoke(WP_REST_Request $request): WP_REST_Response
    {
        $token = self::bearerToken();
        if ($token !== null) {
            delete_transient(self::tokenKey($token));
        }

        return self::noStore(['revoked' => true]);
    }

    /** Authenticate only Pocket REST requests carrying one of our opaque tokens. */
    public static function authenticateBearer($userId): int
    {
        if (is_int($userId) && $userId > 0) {
            return $userId;
        }
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        if (strpos($uri, '/gmrc-pocket/v1/') === false) {
            return is_numeric($userId) ? (int) $userId : 0;
        }

        $token = self::bearerToken();
        if ($token === null) {
            return is_numeric($userId) ? (int) $userId : 0;
        }
        $record = get_transient(self::tokenKey($token));
        if (! is_array($record) || ! isset($record['user_id'], $record['expires_at']) || (int) $record['expires_at'] < time()) {
            return 0;
        }

        return (int) $record['user_id'];
    }

    public static function isNativeReturnRoute(string $route): bool
    {
        return preg_match('#^native-auth/[a-f0-9]{48}$#', trim($route, '/')) === 1;
    }

    public static function requestIdFromReturnRoute(string $route): ?string
    {
        $route = trim($route, '/');
        return self::isNativeReturnRoute($route) ? substr($route, strlen('native-auth/')) : null;
    }

    public static function browserCompletionUrl(string $requestId): string
    {
        return add_query_arg('gmrc_native_handoff', $requestId, home_url('/'));
    }

    public static function handleBrowserCompletion(): void
    {
        $requestId = isset($_GET['gmrc_native_handoff']) && is_scalar($_GET['gmrc_native_handoff'])
            ? sanitize_text_field(wp_unslash((string) $_GET['gmrc_native_handoff'])) : '';
        if ($requestId === '') {
            return;
        }
        if (! preg_match('/^[a-f0-9]{48}$/', $requestId)) {
            wp_die('The native sign-in request is invalid.', 'Pocket Companion', ['response' => 400]);
        }
        if (! is_user_logged_in()) {
            $gateUrl = add_query_arg([
                'gate' => 'login',
                'return_route' => 'native-auth/' . $requestId,
            ], home_url('/companion/'));
            wp_safe_redirect($gateUrl);
            exit;
        }

        $target = self::completeBrowserHandoff($requestId);
        if ($target instanceof WP_Error) {
            wp_die(esc_html($target->get_error_message()), 'Pocket Companion', ['response' => (int) ($target->get_error_data()['status'] ?? 400)]);
        }

        // The target is a fixed app scheme plus server-generated code/state only.
        header('Cache-Control: no-store, private');
        header('Location: ' . $target, true, 302);
        exit;
    }

    private static function bearerToken(): ?string
    {
        $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? trim((string) $_SERVER['HTTP_AUTHORIZATION']) : '';
        if (! preg_match('/^Bearer\s+([A-Za-z0-9_-]{43})$/i', $header, $matches)) {
            return null;
        }
        return $matches[1];
    }

    private static function pkceChallenge(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    private static function randomToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private static function requestKey(string $requestId): string { return 'gmrc_native_req_' . hash('sha256', $requestId); }
    private static function codeKey(string $code): string { return 'gmrc_native_code_' . hash('sha256', $code); }
    private static function tokenKey(string $token): string { return 'gmrc_native_token_' . hash('sha256', $token); }

    private static function noStore(array $data): WP_REST_Response
    {
        return new WP_REST_Response($data, 200, ['Cache-Control' => 'private, no-store']);
    }

    private static function error(string $code, string $message, int $status): WP_Error
    {
        return new WP_Error($code, $message, ['status' => $status]);
    }
}
