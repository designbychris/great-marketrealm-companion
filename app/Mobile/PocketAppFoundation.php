<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

defined('ABSPATH') || exit;

/** Phase III.M.5A: installable, browser-safe Pocket Companion application shell. */
final class PocketAppFoundation
{
    private const ASSET_QUERY = 'gmrc_pocket_asset';

    public static function register(): void
    {
        add_action('wp_head', [self::class, 'printHead'], 2);
        add_action('template_redirect', [self::class, 'serveVirtualAsset'], 0);
    }

    public static function printHead(): void
    {
        global $post;
        if (! $post instanceof \WP_Post || ! has_shortcode((string) $post->post_content, 'gmrc_pocket_companion')) {
            return;
        }

        $startUrl = get_permalink($post) ?: home_url('/');
        $manifest = self::manifestUrl($startUrl);
        $icon = plugins_url('assets/images/pocket/app-icon-192.png', GMRC_PLUGIN_FILE);

        echo '<link rel="manifest" href="' . esc_url($manifest) . '">' . "\n";
        echo '<meta name="theme-color" content="#192d22">' . "\n";
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
        echo '<meta name="apple-mobile-web-app-title" content="MarketRealm Pocket">' . "\n";
        echo '<link rel="apple-touch-icon" sizes="192x192" href="' . esc_url($icon) . '">' . "\n";
    }

    public static function manifestUrl(string $startUrl): string
    {
        return add_query_arg([
            self::ASSET_QUERY => 'manifest',
            'start_url' => rawurlencode($startUrl),
        ], home_url('/'));
    }

    public static function serviceWorkerUrl(): string
    {
        return add_query_arg(self::ASSET_QUERY, 'service-worker', home_url('/'));
    }

    public static function serveVirtualAsset(): void
    {
        $asset = isset($_GET[self::ASSET_QUERY]) ? sanitize_key(wp_unslash($_GET[self::ASSET_QUERY])) : '';
        if ($asset === 'manifest') {
            self::serveManifest();
        }
        if ($asset === 'service-worker') {
            self::serveServiceWorker();
        }
    }

    private static function serveManifest(): void
    {
        $requested = isset($_GET['start_url']) ? rawurldecode((string) wp_unslash($_GET['start_url'])) : home_url('/');
        $home = wp_parse_url(home_url('/'));
        $start = wp_parse_url($requested);
        if (! is_array($start) || ($start['host'] ?? '') !== ($home['host'] ?? '')) {
            $requested = home_url('/');
        }

        $icon192 = plugins_url('assets/images/pocket/app-icon-192.png', GMRC_PLUGIN_FILE);
        $icon512 = plugins_url('assets/images/pocket/app-icon-512.png', GMRC_PLUGIN_FILE);
        $manifest = [
            'id' => '/great-marketrealm-pocket-companion',
            'name' => 'The Great MarketRealm Pocket Companion',
            'short_name' => 'MarketRealm Pocket',
            'description' => 'Your Great MarketRealm adventurer, dice and spellbook in your pocket.',
            'start_url' => esc_url_raw($requested),
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#fff4d9',
            'theme_color' => '#192d22',
            'orientation' => 'any',
            'icons' => [
                ['src' => $icon192, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => $icon512, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ];

        status_header(200);
        nocache_headers();
        header('Content-Type: application/manifest+json; charset=utf-8');
        echo wp_json_encode($manifest, JSON_UNESCAPED_SLASHES);
        exit;
    }

    private static function serveServiceWorker(): void
    {
        $logo = plugins_url('assets/images/pocket/greatmarketrealmlogo.png', GMRC_PLUGIN_FILE);
        $icon192 = plugins_url('assets/images/pocket/app-icon-192.png', GMRC_PLUGIN_FILE);
        $icon512 = plugins_url('assets/images/pocket/app-icon-512.png', GMRC_PLUGIN_FILE);
        $assets = wp_json_encode([$logo, $icon192, $icon512], JSON_UNESCAPED_SLASHES);

        status_header(200);
        nocache_headers();
        header('Content-Type: application/javascript; charset=utf-8');
        header('Service-Worker-Allowed: /');
        echo "const CACHE='gmrc-pocket-static-v1';\n";
        echo 'const STATIC=' . $assets . ";\n";
        echo "self.addEventListener('install',event=>{event.waitUntil(caches.open(CACHE).then(cache=>cache.addAll(STATIC)).then(()=>self.skipWaiting()));});\n";
        echo "self.addEventListener('activate',event=>{event.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(key=>key.startsWith('gmrc-pocket-static-')&&key!==CACHE).map(key=>caches.delete(key)))).then(()=>self.clients.claim()));});\n";
        // Private pages, REST responses and gameplay actions remain network-only. Only bundled public artwork is cached.
        // Navigations receive a generated, non-cached connection guard if the network is unavailable.
        echo "const OFFLINE='<!doctype html><html><head><meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"><meta name=\"theme-color\" content=\"#192d22\"><title>MarketRealm Pocket — Offline</title><style>body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;box-sizing:border-box;background:#192d22;color:#30291d;font:16px system-ui,sans-serif}.card{max-width:430px;padding:28px;border:3px solid #b68b44;border-radius:18px;background:#fff4d9;text-align:center}h1{color:#354a32;font-size:1.7rem}button{min-height:48px;padding:.75rem 1rem;border:0;border-radius:9px;background:#354a32;color:white;font:inherit;font-weight:700}</style></head><body><main class=\"card\"><p>THE GREAT MARKETREALM</p><h1>Pocket Companion</h1><p><strong>The Guild is out of reach.</strong></p><p>Your character data has not been cached. Reconnect to continue safely.</p><button onclick=\"location.reload()\">Try again</button></main></body></html>';\n";
        echo "self.addEventListener('fetch',event=>{if(event.request.method!=='GET')return;const url=new URL(event.request.url);if(url.origin!==self.location.origin)return;if(event.request.mode==='navigate'){event.respondWith(fetch(event.request).catch(()=>new Response(OFFLINE,{status:503,headers:{'Content-Type':'text/html; charset=utf-8','Cache-Control':'no-store'}})));return;}if(STATIC.includes(url.href)){event.respondWith(caches.match(event.request).then(hit=>hit||fetch(event.request)));}});\n";
        exit;
    }
}
