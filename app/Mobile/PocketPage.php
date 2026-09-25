<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

defined('ABSPATH') || exit;

/** Browser-only first-login prototype. Native app authentication is a separate phase. */
final class PocketPage
{
    public static function register(): void
    {
        add_shortcode('gmrc_pocket_companion', [self::class, 'render']);
    }

    public static function render(): string
    {
        $pageUrl = get_permalink() ?: home_url('/');
        if (! is_user_logged_in()) {
            // Reuse the existing front-end Guild Gate and all its security checks,
            // including any configured bot protection; never submit passwords to a new handler.
            $gateUrl = add_query_arg(
                ['gate' => 'login', 'return_route' => 'pocket'],
                home_url('/companion/')
            );
            $logo = plugins_url('assets/images/pocket/greatmarketrealmlogo.png', GMRC_PATH . 'great-marketrealm-companion.php');
            return '<section class="gmrc-pocket-entry" aria-labelledby="gmrc-pocket-entry-title">'
                . '<div class="gmrc-pocket-entry__panel">'
                . '<img class="gmrc-pocket-entry__logo" src="' . esc_url($logo) . '" alt="The Great Marketrealm" loading="eager">'
                . '<p class="gmrc-pocket-entry__eyebrow">THE GREAT MARKETREALM</p>'
                . '<h2 id="gmrc-pocket-entry-title">Pocket Companion</h2>'
                . '<p>Your adventurer, your dice, your magic — ready wherever the road takes you.</p>'
                . '<a class="gmrc-pocket-entry__button" href="' . esc_url($gateUrl) . '">Enter the Pocket Guild Gate</a>'
                . '</div></section>'
                . '<style>.gmrc-pocket-entry{min-height:70svh;display:grid;place-items:center;padding:clamp(1rem,4vw,3rem);background:linear-gradient(145deg,#233b29e8,#4b5832dd),radial-gradient(circle at 50% 20%,#b89b55,#34452e);color:#322b1e}.gmrc-pocket-entry *{box-sizing:border-box}.gmrc-pocket-entry__panel{width:min(100%,440px);padding:clamp(1.2rem,5vw,2.5rem);text-align:center;border:3px solid #b68b44;border-radius:18px;background:#fff4d9;box-shadow:0 16px 50px #0005}.gmrc-pocket-entry__logo{display:block;width:min(100%,340px);height:auto;max-height:190px;object-fit:contain;margin:0 auto 1rem}.gmrc-pocket-entry__eyebrow{font-size:.78rem;font-weight:800;letter-spacing:.14em;color:#52683d}.gmrc-pocket-entry h2{font-size:clamp(1.6rem,6vw,2.2rem);color:#354a32;margin:.5rem 0}.gmrc-pocket-entry p{line-height:1.55}.gmrc-pocket-entry__button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;margin-top:1rem;padding:.8rem 1.2rem;border-radius:9px;background:#354a32;color:#fff!important;font-weight:700;text-decoration:none}.gmrc-pocket-entry__button:focus-visible{outline:3px solid #ad6b14;outline-offset:4px}@media(prefers-reduced-motion:reduce){.gmrc-pocket-entry *{scroll-behavior:auto}}
/* III.M.4C.2: one coordinated Pocket dock; supersedes the legacy independent fixed/sticky positions. */
.gmrc-pocket-bottom-dock{display:grid;gap:.35rem;min-width:0}
.gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__more-menu{order:2}
.gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__tabs{order:3}
.gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock{order:1}
.gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock .gmrc-pocket-dice{padding:0;max-height:none;overflow:visible}
.gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock .gmrc-pocket-dice>summary{margin:0;padding:.5rem .7rem;min-height:48px}
.gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock .gmrc-pocket-dice:not([open]) .gmrc-pocket-dice__body{display:none}
@media(max-width:649px){
 .gmrc-pocket--character-open>.gmrc-pocket-nav{display:none}
 .gmrc-pocket-dashboard{padding-bottom:calc(132px + env(safe-area-inset-bottom,0px))}
 .gmrc-pocket-bottom-dock{position:fixed;left:max(8px,env(safe-area-inset-left,0px));right:max(8px,env(safe-area-inset-right,0px));bottom:env(safe-area-inset-bottom,0px);z-index:30;gap:0;padding:.25rem;background:#304a35;border:1px solid #c5a66c;border-radius:14px 14px 0 0;box-shadow:0 -5px 24px #17291f55;min-width:0}
 .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__tabs,.gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock,.gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__more-menu{position:static;inset:auto;left:auto;right:auto;top:auto;bottom:auto;width:100%;margin:0;box-shadow:none}
 .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__tabs{grid-template-columns:repeat(5,minmax(0,1fr));padding:.3rem;gap:.2rem;z-index:auto}
 .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__tab{font-size:clamp(.64rem,2.7vw,.85rem);padding:.35rem .1rem;min-height:48px}
 .gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock{padding:.2rem;border:0;background:transparent;z-index:auto}
 .gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock .gmrc-pocket-dice{max-height:min(60dvh,520px);overflow-y:auto;overscroll-behavior:contain}
 .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__more-menu{z-index:auto}
}
</style>';
        }

        $id = 'gmrc-pocket-' . wp_unique_id();
        $config = [
            'session' => esc_url_raw(rest_url('gmrc-pocket/v1/session')),
            'characters' => esc_url_raw(rest_url('gmrc-pocket/v1/characters')),
            'vitalityBase' => esc_url_raw(rest_url('gmrc-pocket/v1/characters/')),
            'spellSlotBase' => esc_url_raw(rest_url('gmrc-pocket/v1/characters/')),
            'nonce' => wp_create_nonce('wp_rest'),
        ];
        $json = wp_json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $html = '<section class="gmrc-pocket" id="' . esc_attr($id) . '" aria-label="Pocket character sheet">'
            . '<header class="gmrc-pocket__header"><div class="gmrc-pocket__brand"><span class="gmrc-pocket-kicker">THE GREAT MARKETREALM</span><h2>Pocket Companion</h2></div>'
            . '<p class="gmrc-pocket-welcome" aria-live="polite">Opening the character ledger…</p></header>'
            . '<nav class="gmrc-pocket-nav" aria-label="Pocket Companion navigation"><button type="button" class="gmrc-pocket-nav__item" data-pocket-nav="home" aria-current="page">⌂ <span>Home</span></button><button type="button" class="gmrc-pocket-nav__item" data-pocket-nav="character" disabled>♙ <span>Character</span></button></nav>'
            . '<div class="gmrc-pocket-status" role="status" aria-live="polite">Loading your characters…</div>'
            . '<div class="gmrc-pocket-characters" aria-label="Your characters"></div>'
            . '<section class="gmrc-pocket-detail" aria-label="Character sheet" hidden></section>'
            . '<button type="button" class="gmrc-pocket-button gmrc-pocket-refresh">Refresh characters</button>'
            . '</section>';
        $html .= <<<'CSS'
<style>
.gmrc-pocket{box-sizing:border-box;max-width:850px;margin:1.25rem auto;padding:clamp(1rem,4vw,2rem);border:2px solid #b48a46;border-radius:20px;background:linear-gradient(145deg,#fff9e9,#eee1c1);color:#30291d;font:inherit;box-shadow:0 10px 30px #241a0e24}.gmrc-pocket *{box-sizing:border-box}.gmrc-pocket__header{border-bottom:2px solid #c9ae79;padding-bottom:.8rem}.gmrc-pocket h2{margin:.2rem 0 .5rem;color:#354a32;font-size:clamp(1.6rem,6vw,2.3rem)}.gmrc-pocket-kicker{font-size:.75rem;letter-spacing:.14em;font-weight:700;color:#52683d}.gmrc-pocket-characters{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,300px),1fr));gap:1rem;margin:1.2rem 0}.gmrc-pocket-character{overflow:hidden;background:#fffaf0;border:2px solid #c5a66c;border-radius:15px;box-shadow:0 4px 12px #3a2c161c}.gmrc-pocket-character__head{padding:1rem;background:#e6ebda;border-bottom:1px solid #c5a66c}.gmrc-pocket-character h3{margin:0;color:#354a32;font-size:clamp(1.2rem,5vw,1.55rem);overflow-wrap:anywhere}.gmrc-pocket-character__body{padding:1rem}.gmrc-pocket-character__label{display:block;font-size:.72rem;letter-spacing:.09em;text-transform:uppercase;font-weight:800;color:#52683d}.gmrc-pocket-hp{display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin:.8rem 0}.gmrc-pocket-hp__tile{padding:.8rem;border:1px solid #d0be97;border-radius:10px;background:#fff}.gmrc-pocket-hp__value{display:block;font-size:1.45rem;font-weight:800;color:#354a32;font-variant-numeric:tabular-nums}.gmrc-pocket-hp__meter{height:9px;overflow:hidden;background:#d9d4c5;border-radius:99px}.gmrc-pocket-hp__fill{display:block;height:100%;background:#628344;border-radius:99px;max-width:100%}.gmrc-pocket-character__note{margin:.85rem 0 0;font-size:.87rem;color:#625943}.gmrc-pocket-button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:.75rem 1rem;border:0;border-radius:9px;background:#354a32;color:#fff!important;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.gmrc-pocket-button:focus-visible{outline:3px solid #a36a16;outline-offset:3px}.gmrc-pocket-status{margin-top:1rem}.gmrc-pocket-status:empty{display:none}@media(max-width:420px){.gmrc-pocket{margin:.5rem auto;padding:1rem;border-radius:13px}.gmrc-pocket-characters{grid-template-columns:minmax(0,1fr)}.gmrc-pocket-hp__value{font-size:1.25rem}}
 /* III.M.4B: progressive app dashboard; the existing controls retain their event handlers. */
.gmrc-pocket-dashboard{display:grid;gap:1rem}.gmrc-pocket-dashboard__hero{display:grid;gap:.8rem;align-items:center;padding:1rem;border:1px solid #c5a66c;border-radius:14px;background:#e6ebda}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__portrait{margin:0 auto;width:min(100%,170px)}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__portrait-fallback{margin:0}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__heading{margin:0}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__subtitle{margin:.25rem 0}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__stats{grid-column:1/-1;margin:0}.gmrc-pocket-dashboard__tabs{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.4rem;position:sticky;top:0;z-index:5;padding:.55rem;background:#fffaf0;border:1px solid #c5a66c;border-radius:12px;box-shadow:0 3px 10px #241a0e22}.gmrc-pocket-dashboard__tab{min-height:48px;padding:.5rem .2rem;border:1px solid #b48a46;border-radius:8px;background:#fff;color:#354a32;font:inherit;font-weight:700;cursor:pointer}.gmrc-pocket-dashboard__tab[aria-selected="true"]{background:#354a32;color:#fff}.gmrc-pocket-dashboard__tab:focus-visible{outline:3px solid #a36a16;outline-offset:2px}.gmrc-pocket-dashboard__panel{min-width:0}.gmrc-pocket-dashboard__panel[hidden]{display:none!important}.gmrc-pocket-dashboard__panel>h4:first-child{margin-top:.2rem}.gmrc-pocket-dashboard__panel>.gmrc-pocket-training{margin:.6rem 0}.gmrc-pocket-dashboard__panel>.gmrc-pocket-dice{margin:.6rem 0}@media(min-width:650px){.gmrc-pocket-dashboard__hero{grid-template-columns:170px minmax(0,1fr)}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__portrait{grid-row:span 2}.gmrc-pocket-dashboard__tabs{grid-template-columns:repeat(6,minmax(0,1fr))}}@media(max-width:649px){.gmrc-pocket-dashboard__tabs{position:sticky;bottom:env(safe-area-inset-bottom,0px);top:auto;grid-template-columns:repeat(3,minmax(0,1fr));padding-bottom:calc(.55rem + env(safe-area-inset-bottom,0px))}.gmrc-pocket-dashboard__hero .gmrc-pocket-detail__stats{grid-template-columns:repeat(3,minmax(0,1fr))}}/* III.M.4C.1: Five-button navigation and shared Diceworks, with one mobile dock stack. */
.gmrc-pocket-dashboard__tabs{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.3rem}.gmrc-pocket-dashboard__tab{min-width:0;min-height:48px;overflow-wrap:anywhere}.gmrc-pocket-dashboard__tab[aria-current=page]{background:#354a32;color:#fff}.gmrc-pocket-dashboard__more-menu{padding:.5rem;background:#fffaf0;border:1px solid #c5a66c;border-radius:10px}.gmrc-pocket-dashboard__more-menu[hidden]{display:none!important}.gmrc-pocket-dashboard__more-menu .gmrc-pocket-dashboard__tab{width:100%}
@media(max-width:649px){.gmrc-pocket--character-open>.gmrc-pocket-nav{display:none}.gmrc-pocket-dashboard{padding-bottom:calc(165px + env(safe-area-inset-bottom,0px))}.gmrc-pocket-dashboard__tabs{position:fixed;top:auto;left:max(8px,env(safe-area-inset-left,0px));right:max(8px,env(safe-area-inset-right,0px));bottom:env(safe-area-inset-bottom,0px);z-index:14;padding:.4rem;grid-template-columns:repeat(5,minmax(0,1fr));gap:.2rem}.gmrc-pocket-dashboard__tab{font-size:.72rem;padding:.4rem .1rem}.gmrc-pocket-dice-dock{bottom:calc(66px + env(safe-area-inset-bottom,0px));z-index:15}.gmrc-pocket-dashboard__more-menu{position:fixed;left:max(8px,env(safe-area-inset-left,0px));right:max(8px,env(safe-area-inset-right,0px));bottom:calc(70px + env(safe-area-inset-bottom,0px));z-index:16}}
@media(min-width:900px){.gmrc-pocket-dashboard__tabs{position:sticky;top:1rem;grid-template-columns:repeat(5,minmax(0,1fr))}}
/* III.M.4C: a single shared Diceworks dock, available on every character section. */
.gmrc-pocket-dashboard{padding-bottom:1rem}.gmrc-pocket-dice-dock{position:sticky;bottom:0;z-index:12;background:#304a35;border:2px solid #c5a66c;border-radius:14px;padding:.4rem;box-shadow:0 -4px 20px #17291f44;color:#fff}.gmrc-pocket-dice-dock .gmrc-pocket-dice{margin:0;background:#fffaf0;color:#304a35;border-radius:10px;max-height:min(65dvh,560px);overflow-y:auto;overscroll-behavior:contain}.gmrc-pocket-dice-dock .gmrc-pocket-dice>summary{min-height:48px;padding:.75rem;cursor:pointer;font-weight:700}.gmrc-pocket-dice-dock .gmrc-pocket-dice__latest{display:block;font-size:.85rem;font-weight:400;margin-top:.15rem;overflow-wrap:anywhere}.gmrc-pocket-dice-dock .gmrc-pocket-dice__body{padding:.65rem}.gmrc-pocket-dice-dock .gmrc-pocket-dice>summary:focus-visible{outline:3px solid #a36a16;outline-offset:-3px}@media(max-width:649px){.gmrc-pocket-dashboard{padding-bottom:calc(90px + env(safe-area-inset-bottom,0px))}.gmrc-pocket-dashboard__tabs{position:sticky;top:0;bottom:auto;padding-bottom:.55rem}.gmrc-pocket-dice-dock{position:fixed;left:max(8px,env(safe-area-inset-left,0px));right:max(8px,env(safe-area-inset-right,0px));bottom:env(safe-area-inset-bottom,0px)}.gmrc-pocket-dice-dock .gmrc-pocket-dice{max-height:65dvh}}@media(prefers-reduced-motion:reduce){.gmrc-pocket-dashboard *{scroll-behavior:auto}}
.gmrc-pocket-training{margin:1rem 0;border:1px solid #c5a66c;border-radius:10px;background:#f1f3e7;padding:.8rem}.gmrc-pocket-training summary{cursor:pointer;font-weight:800;min-height:44px;padding:.6rem;color:#354a32}.gmrc-pocket-training__list{display:grid;gap:.5rem;margin-top:.5rem}.gmrc-pocket-training__row{display:flex;align-items:center;gap:.6rem;justify-content:space-between;padding:.55rem;border:1px solid #c5a66c;border-radius:8px;background:#fff;flex-wrap:wrap}.gmrc-pocket-training__name{flex:1;min-width:8rem}.gmrc-pocket-training__name small{display:block;color:#52683d}.gmrc-pocket-training__roll{min-height:44px;padding:.45rem .8rem;border:0;border-radius:7px;background:#354a32;color:#fff;font:inherit;font-weight:700;cursor:pointer}.gmrc-pocket-training__roll:focus-visible,.gmrc-pocket-training summary:focus-visible{outline:3px solid #a36a16;outline-offset:2px} .gmrc-pocket-detail[hidden],.gmrc-pocket-characters[hidden]{display:none!important}.gmrc-pocket-detail{margin:1.2rem 0;padding:1rem;border:2px solid #b48a46;border-radius:15px;background:#fffaf0}.gmrc-pocket-detail__portrait{display:block;width:min(100%,220px);aspect-ratio:4/5;object-fit:contain;margin:1rem auto;border:2px solid #b48a46;border-radius:12px;background:#eee1c1}.gmrc-pocket-detail__portrait-fallback{text-align:center;padding:1.5rem;color:#52683d}.gmrc-pocket-detail__heading{margin:.5rem 0;color:#354a32}.gmrc-pocket-detail__subtitle{color:#52683d}.gmrc-pocket-detail__stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem;margin:1rem 0}.gmrc-pocket-detail__stat{padding:.75rem .35rem;border:1px solid #c5a66c;border-radius:9px;text-align:center;background:#fff}.gmrc-pocket-detail__stat strong{display:block;font-size:1.25rem;color:#354a32}.gmrc-pocket-detail__abilities{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem}.gmrc-pocket-detail__ability{padding:.65rem .2rem;text-align:center;border:1px solid #c5a66c;border-radius:9px;background:#f1f3e7}.gmrc-pocket-detail__ability strong{display:block;font-size:1.2rem}.gmrc-pocket-detail__ability small{display:block}.gmrc-pocket-vitality{display:grid;gap:.65rem;padding:1rem;margin:1rem 0;border:1px solid #c5a66c;border-radius:10px;background:#f1f3e7}.gmrc-pocket-vitality label{font-weight:700}.gmrc-pocket-vitality input{width:100%;min-height:44px;padding:.5rem;border:1px solid #b48a46;border-radius:7px;background:#fff;color:#30291d;font:inherit}.gmrc-pocket-vitality__actions{display:grid;grid-template-columns:1fr 1fr;gap:.6rem}.gmrc-pocket-vitality__quick{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.6rem}.gmrc-pocket-vitality__quick button{width:100%}.gmrc-pocket-vitality__amount{max-width:100%}.gmrc-pocket-vitality__message{min-height:1.4em}.gmrc-pocket-character__open{width:100%;margin-top:1rem}@media(max-width:360px){.gmrc-pocket-detail__stats,.gmrc-pocket-detail__abilities{grid-template-columns:repeat(2,minmax(0,1fr))}}

.gmrc-pocket-dice{margin:1rem 0;padding:1rem;border:1px solid #c5a66c;border-radius:10px;background:#f1f3e7}.gmrc-pocket-dice summary{cursor:pointer;font-weight:800;color:#354a32;padding:.3rem}.gmrc-pocket-dice__body{display:grid;gap:.8rem;margin-top:.8rem}.gmrc-pocket-dice__types{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.45rem}.gmrc-pocket-dice__types button{min-height:44px;border:1px solid #b48a46;border-radius:8px;background:#fff;color:#354a32;font:inherit;font-weight:700;cursor:pointer}.gmrc-pocket-dice__types button[aria-pressed=true]{background:#354a32;color:#fff}.gmrc-pocket-dice__fields{display:grid;grid-template-columns:1fr 1fr;gap:.6rem}.gmrc-pocket-dice__fields label{display:grid;gap:.3rem;font-weight:700}.gmrc-pocket-dice__stepper{display:flex;gap:.3rem;align-items:center}.gmrc-pocket-dice__stepper input{flex:1;width:0}.gmrc-pocket-dice__stepper button{min-width:44px;min-height:44px;border:1px solid #b48a46;border-radius:7px;background:#fff;color:#354a32;font:inherit;font-weight:800;cursor:pointer}.gmrc-pocket-detail__ability .gmrc-pocket-ability-roll{display:block;width:calc(100% - .5rem);margin:.45rem auto 0;min-height:44px;padding:.35rem;border:1px solid #354a32;border-radius:7px;background:#354a32;color:#fff;font:inherit;font-weight:700;cursor:pointer}.gmrc-pocket-dice__celebration{font-weight:800;color:#354a32}.gmrc-pocket-dice__celebration--animated{animation:gmrc-pocket-dice-pop .45s ease-out}@keyframes gmrc-pocket-dice-pop{0%{transform:scale(.95)}60%{transform:scale(1.06)}100%{transform:scale(1)}}.gmrc-pocket-dice input{width:100%;min-width:0;min-height:44px;padding:.45rem;border:1px solid #b48a46;border-radius:7px;font:inherit}.gmrc-pocket-dice__result{padding:.7rem;border:1px solid #c5a66c;border-radius:8px;background:#fff;overflow-wrap:anywhere}.gmrc-pocket-dice__result strong{font-size:1.6rem;color:#354a32}.gmrc-pocket-dice__history{margin:0;padding-left:1.4rem}.gmrc-pocket-dice__history li{margin:.3rem 0}.gmrc-pocket-dice button:focus-visible,.gmrc-pocket-dice summary:focus-visible{outline:3px solid #a36a16;outline-offset:2px}@media(max-width:360px){.gmrc-pocket-dice__types{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(prefers-reduced-motion:reduce){.gmrc-pocket-dice *{animation:none!important;transition:none!important}}

/* III.M.4A: responsive app foundation, scoped to Pocket only. */
.gmrc-pocket{--pocket-forest:#17291f;--pocket-gold:#e2c17c;max-width:1180px;min-height:70svh;overflow:clip;background:linear-gradient(155deg,#fffaf0,#e9dfc7);padding:clamp(1rem,2.6vw,2rem);border-radius:24px}
.gmrc-pocket__header{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.6rem 1.5rem;margin:-2rem -2rem 1rem;padding:1.2rem 2rem;background:var(--pocket-forest);color:#fff;border-bottom:3px solid #b48a46}
.gmrc-pocket__header h2{color:#fff;margin:.1rem 0}.gmrc-pocket__header .gmrc-pocket-kicker{color:var(--pocket-gold)}.gmrc-pocket-welcome{color:#f1e6cb;margin:0}
.gmrc-pocket-nav{display:flex;gap:.5rem;margin:0 0 1.25rem;padding:.4rem;background:#e4d7b9;border:1px solid #c5a66c;border-radius:14px}
.gmrc-pocket-nav__item{flex:1;display:flex;gap:.45rem;align-items:center;justify-content:center;min-height:48px;border:0;border-radius:10px;background:transparent;color:#304a35;font:inherit;font-weight:800;cursor:pointer}
.gmrc-pocket-nav__item[aria-current="page"]{background:#304a35;color:#fff}.gmrc-pocket-nav__item:disabled{opacity:.45;cursor:not-allowed}.gmrc-pocket-nav__item:focus-visible{outline:3px solid #a36a16;outline-offset:2px}
.gmrc-pocket-entry{min-height:75svh;border-radius:24px;overflow:hidden}.gmrc-pocket-entry__panel{border-radius:22px}.gmrc-pocket-entry__button{width:100%}
@media(max-width:600px){.gmrc-pocket{margin:0 auto;padding:1rem 1rem calc(6rem + env(safe-area-inset-bottom,0px));border-radius:0;min-height:100svh}.gmrc-pocket__header{margin:-1rem -1rem 1rem;padding:1rem}.gmrc-pocket-nav{position:sticky;bottom:env(safe-area-inset-bottom,0px);z-index:20;box-shadow:0 4px 20px #0003}.gmrc-pocket-entry{min-height:100svh;border-radius:0}.gmrc-pocket-welcome{font-size:.9rem}}
@media(min-width:900px){.gmrc-pocket{display:grid;grid-template-columns:180px minmax(0,1fr);column-gap:1.5rem;align-content:start}.gmrc-pocket__header{grid-column:1/-1}.gmrc-pocket-nav{grid-column:1;grid-row:2;flex-direction:column;align-self:start;position:sticky;top:1rem}.gmrc-pocket-nav__item{justify-content:flex-start;padding:0 1rem}.gmrc-pocket-status,.gmrc-pocket-characters,.gmrc-pocket-detail,.gmrc-pocket-refresh{grid-column:2}}
@media(prefers-reduced-motion:reduce){.gmrc-pocket *{scroll-behavior:auto!important;transition-duration:0s!important}}
/* III.M.4C.3: Pocket owns its viewport while a character is open. The overlay
   intentionally covers the host site's header without changing the site theme. */
@media(max-width:649px){
 html:has(.gmrc-pocket--character-open),body:has(.gmrc-pocket--character-open){overflow:hidden!important}
 .gmrc-pocket.gmrc-pocket--character-open{position:fixed!important;inset:0!important;width:100%!important;max-width:none!important;height:100dvh!important;min-height:0!important;margin:0!important;padding:0!important;border:0!important;border-radius:0!important;z-index:2147483000!important;display:flex!important;flex-direction:column!important;overflow:hidden!important;background:#fffaf0!important;box-sizing:border-box!important}
 .gmrc-pocket--character-open>.gmrc-pocket__header{position:relative!important;flex:0 0 auto!important;margin:0!important;padding:calc(.55rem + env(safe-area-inset-top,0px)) 1rem .55rem!important;min-height:0!important;z-index:1!important}
 .gmrc-pocket--character-open>.gmrc-pocket__header h2{font-size:1.05rem!important;line-height:1.25!important}
 .gmrc-pocket--character-open>.gmrc-pocket__header .gmrc-pocket-kicker{font-size:.65rem!important}
 .gmrc-pocket--character-open>.gmrc-pocket__header .gmrc-pocket-welcome{display:none!important}
 .gmrc-pocket--character-open>.gmrc-pocket-nav,.gmrc-pocket--character-open>.gmrc-pocket-status,.gmrc-pocket--character-open>.gmrc-pocket-characters,.gmrc-pocket--character-open>.gmrc-pocket-refresh{display:none!important}
 .gmrc-pocket--character-open>.gmrc-pocket-detail{display:flex!important;flex:1 1 auto!important;min-height:0!important;min-width:0!important;padding:0!important;margin:0!important;overflow:hidden!important}
 .gmrc-pocket--character-open>.gmrc-pocket-detail[hidden]{display:none!important}
 .gmrc-pocket--character-open .gmrc-pocket-dashboard{display:flex!important;flex-direction:column!important;flex:1 1 auto!important;min-height:0!important;min-width:0!important;width:100%!important;padding:0!important;margin:0!important;gap:0!important;overflow:hidden!important}
 .gmrc-pocket--character-open .gmrc-pocket-dashboard__viewport{flex:1 1 auto!important;min-height:0!important;min-width:0!important;overflow-y:auto!important;overscroll-behavior:contain!important;-webkit-overflow-scrolling:touch;padding:1rem 1rem 1.5rem!important;box-sizing:border-box!important}
 .gmrc-pocket--character-open .gmrc-pocket-dashboard__hero{margin:.5rem 0 1rem!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock{position:relative!important;inset:auto!important;left:auto!important;right:auto!important;top:auto!important;bottom:auto!important;flex:0 0 auto!important;width:100%!important;min-width:0!important;max-height:min(75dvh,650px)!important;overflow:hidden!important;box-sizing:border-box!important;display:flex!important;flex-direction:column!important;gap:.2rem!important;margin:0!important;padding:.25rem .4rem calc(.25rem + env(safe-area-inset-bottom,0px))!important;border-radius:14px 14px 0 0!important;z-index:2!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock{position:relative!important;inset:auto!important;flex:0 1 auto!important;min-height:0!important;overflow:hidden!important;padding:0!important;border:0!important;margin:0!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dice{display:flex!important;flex-direction:column!important;min-height:0!important;max-height:min(53dvh,460px)!important;overflow:hidden!important;margin:0!important;padding:0!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dice>summary{display:list-item!important;flex:0 0 auto!important;min-height:44px!important;padding:.4rem .65rem!important;margin:0!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dice__body{min-height:0!important;overflow-y:auto!important;overscroll-behavior:contain!important;-webkit-overflow-scrolling:touch;padding:.5rem!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dice:not([open]) .gmrc-pocket-dice__body{display:none!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__tabs{position:relative!important;inset:auto!important;flex:0 0 auto!important;display:grid!important;grid-template-columns:repeat(5,minmax(0,1fr))!important;width:100%!important;min-width:0!important;margin:0!important;padding:.2rem!important;gap:.2rem!important;box-sizing:border-box!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__more-menu{position:relative!important;inset:auto!important;flex:0 0 auto!important;width:100%!important;margin:0!important;padding:.2rem!important;box-sizing:border-box!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__more-menu[hidden]{display:none!important}
 .gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dashboard__tab{min-width:0!important;min-height:44px!important;padding:.3rem 0!important;font-size:clamp(.61rem,2.5vw,.85rem)!important;overflow-wrap:anywhere!important}
}
</style>
CSS;
        $html .= <<<'JS'
<script>
(function(){"use strict";
const root=document.getElementById(POCKET_ROOT_ID);if(!root)return;
const config=POCKET_CONFIG;
const homeNav=root.querySelector('[data-pocket-nav="home"]'),characterNav=root.querySelector('[data-pocket-nav="character"]');
let selectedCharacter=null;function setPocketView(view){homeNav.setAttribute('aria-current',view==='home'?'page':'false');characterNav.setAttribute('aria-current',view==='character'?'page':'false');}
let currentCharacters=[];const status=root.querySelector('.gmrc-pocket-status'),welcome=root.querySelector('.gmrc-pocket-welcome'),list=root.querySelector('.gmrc-pocket-characters'),refresh=root.querySelector('.gmrc-pocket-refresh'),detail=root.querySelector('.gmrc-pocket-detail');
function el(tag,className,value){const node=document.createElement(tag);if(className)node.className=className;if(value!==undefined)node.textContent=String(value);return node;}
async function request(url){const response=await fetch(url,{credentials:'same-origin',headers:{'X-WP-Nonce':config.nonce,'Accept':'application/json'},cache:'no-store'});if(!response.ok)throw new Error(response.status===401||response.status===403?'Your session has expired. Please sign in again.':'Could not reach the character ledger. Please try again.');return response.json();}
async function saveVitality(character,current,temporary){const hp=character.hp||{};const response=await fetch(config.vitalityBase+encodeURIComponent(character.id)+'/vitality',{method:'POST',credentials:'same-origin',cache:'no-store',headers:{'X-WP-Nonce':config.nonce,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({current,temporary,expected_current:hp.current,expected_temporary:hp.temporary})});const data=await response.json().catch(()=>({}));if(!response.ok)throw new Error(data.message||'Unable to save HP. Please refresh and try again.');character.hp=data.hp;return data.hp;}
async function changeSpellSlot(character,slot,action){
 const response=await fetch(config.spellSlotBase+encodeURIComponent(character.id)+'/spell-slots',{
  method:'POST',credentials:'same-origin',cache:'no-store',headers:{'X-WP-Nonce':config.nonce,'Accept':'application/json','Content-Type':'application/json'},
  body:JSON.stringify({level:slot.level,action,expected_remaining:slot.remaining})
 });
 const data=await response.json().catch(()=>({}));
 if(!response.ok)throw new Error(data.message||'Unable to update spell slots. Refresh and try again.');
 character.spellcasting.slots=data.slots;return data.slots;
}
function vitalityControls(character){
 const hp=character.hp||{};const form=el('form','gmrc-pocket-vitality');form.append(el('h4','','Adventuring Measures'));
 const fields=[];
 for(const [label,max,value] of [['Current HP',Number(hp.maximum),hp.current],['Temporary HP',999,hp.temporary]]){
  const wrap=el('label','',label),input=el('input');input.type='number';input.inputMode='numeric';input.min='0';input.max=String(max);input.step='1';input.required=true;input.value=String(value);wrap.append(input);fields.push(input);form.append(wrap);
 }
 const amountLabel=el('label','','Damage or healing amount');const amount=el('input','gmrc-pocket-vitality__amount');amount.type='number';amount.inputMode='numeric';amount.min='1';amount.max='9999';amount.step='1';amount.value='5';amountLabel.append(amount);form.append(amountLabel);
 const actions=el('div','gmrc-pocket-vitality__quick');const damage=el('button','gmrc-pocket-button','− Apply damage'),heal=el('button','gmrc-pocket-button','+ Apply healing');damage.type=heal.type='button';actions.append(damage,heal);form.append(actions);
 const save=el('button','gmrc-pocket-button','Save HP');save.type='submit';const message=el('div','gmrc-pocket-vitality__message');message.setAttribute('role','status');message.setAttribute('aria-live','polite');form.append(save,message);
 let busy=false,stale=false;
 function setBusy(value){busy=value;for(const control of [save,damage,heal,...fields,amount])control.disabled=value||stale;}
 function values(){const current=Number(fields[0].value),temporary=Number(fields[1].value);if(fields.some(input=>!input.validity.valid)||![current,temporary].every(Number.isSafeInteger))throw new Error('Enter valid whole-number HP values.');return {current,temporary};}
 async function persist(current,temporary,success){if(busy||stale)return;setBusy(true);message.textContent='Saving…';try{await saveVitality(character,current,temporary);showDetail(character);const feedback=detail.querySelector('.gmrc-pocket-vitality__message');if(feedback)feedback.textContent=success;}catch(error){message.textContent=error.message||'Unable to save HP.';if(error.message&&error.message.includes('changed elsewhere')){stale=true;message.textContent+=' Use Refresh characters to load the latest HP before trying again.';}}finally{setBusy(false);}}
 form.addEventListener('submit',event=>{event.preventDefault();try{const {current,temporary}=values();persist(current,temporary,'HP saved.');}catch(error){message.textContent=error.message;}});
 async function adjust(kind){if(busy||stale)return;try{const {current,temporary}=values(),n=Number(amount.value);if(!amount.validity.valid||!Number.isSafeInteger(n)||n<1||n>9999)throw new Error('Enter a damage or healing amount between 1 and 9999.');
 // Damage consumes temporary HP first. Healing restores current HP only, up to maximum.
 const absorbed=kind==='damage'?Math.min(temporary,n):0;
 const nextTemp=temporary-absorbed;
 const nextCurrent=kind==='damage'?Math.max(0,current-(n-absorbed)):Math.min(Number(hp.maximum),current+n);
 await persist(nextCurrent,kind==='damage'?nextTemp:temporary,kind==='damage'?n+' damage applied ('+absorbed+' absorbed by temporary HP).':n+' healing applied.');
 }catch(error){message.textContent=error.message||'Unable to adjust HP.';}}
 damage.addEventListener('click',()=>adjust('damage'));heal.addEventListener('click',()=>adjust('healing'));return form;
}

// Pocket Diceworks follows the main Guild Diceworks supported dice, 20-die limit,
// and unbiased rejection-sampling algorithm. Rolls are local to this browser session.
function pocketSecureDie(sides){
 const cryptoSource=window.crypto;
 if(!cryptoSource||typeof cryptoSource.getRandomValues!=='function')throw new Error('Secure dice are unavailable in this browser. Use a secure HTTPS connection.');
 const range=0x100000000,limit=range-(range%sides),values=new Uint32Array(1);
 let value=limit;while(value>=limit){cryptoSource.getRandomValues(values);value=values[0];}
 return (value%sides)+1;
}
function pocketDiceworks(){
 const tray=el('details','gmrc-pocket-dice'),summary=el('summary','','Guild Diceworks 🎲'),latest=el('span','gmrc-pocket-dice__latest','Ready to roll');summary.append(latest);tray.append(summary);
 const body=el('div','gmrc-pocket-dice__body'),types=el('div','gmrc-pocket-dice__types');types.setAttribute('role','group');types.setAttribute('aria-label','Choose a die');
 let sides=20;const diceButtons=[];
 for(const die of [4,6,8,10,12,20,100]){const button=el('button','','d'+die);button.type='button';button.setAttribute('aria-pressed',String(die===sides));button.addEventListener('click',()=>{sides=die;for(const entry of diceButtons)entry.setAttribute('aria-pressed',String(entry===button));});diceButtons.push(button);types.append(button);}
 body.append(types);const fields=el('div','gmrc-pocket-dice__fields');
 function field(label,value,min,max){const wrap=el('label','',label),input=el('input');input.type='number';input.inputMode='numeric';input.step='1';input.min=String(min);input.max=String(max);input.required=true;input.value=String(value);wrap.append(input);fields.append(wrap);return input;}
 const count=field('Number of dice',1,1,20),modifier=field('Modifier',0,-999,999);
 function addStepper(input,min,max){const wrap=el('div','gmrc-pocket-dice__stepper'),down=el('button','','−'),up=el('button','','+');down.type=up.type='button';down.setAttribute('aria-label','Decrease '+input.parentElement.firstChild.textContent);up.setAttribute('aria-label','Increase '+input.parentElement.firstChild.textContent);input.parentElement.insertBefore(wrap,input);wrap.append(down,input,up);for(const [button,delta] of [[down,-1],[up,1]])button.addEventListener('click',()=>{const n=Number(input.value);input.value=String(Math.max(min,Math.min(max,(Number.isSafeInteger(n)?n:0)+delta)));input.dispatchEvent(new Event('input',{bubbles:true}));});}
 addStepper(count,1,20);addStepper(modifier,-999,999);body.append(fields);
 const roll=el('button','gmrc-pocket-button','Roll dice');roll.type='button';body.append(roll);
 const result=el('div','gmrc-pocket-dice__result','Choose your dice and roll.');result.setAttribute('role','status');result.setAttribute('aria-live','polite');body.append(result);
 const heading=el('h5','','Recent rolls'),history=el('ol','gmrc-pocket-dice__history');body.append(heading,history);tray.append(body);
 function performRoll(die,n,mod,label){
  if(!Number.isSafeInteger(n)||n<1||n>20||!Number.isSafeInteger(mod)||mod< -999||mod>999){result.textContent='Enter 1–20 dice and a whole-number modifier from −999 to +999.';return;}
  try{const values=[];for(let i=0;i<n;i++)values.push(pocketSecureDie(die));const total=values.reduce((sum,value)=>sum+value,mod);
   const descriptor=label||n+'d'+die;latest.textContent=descriptor+' · '+total;result.replaceChildren(el('strong','',total),el('div','',descriptor+(mod>=0?' + ':' − ')+Math.abs(mod)+' · ['+values.join(', ')+']'));
   if(die===20&&n===1&&(values[0]===20||values[0]===1)){const celebration=el('div','gmrc-pocket-dice__celebration',values[0]===20?'✨ Natural 20! ✨':'🎲 Natural 1!');if(!window.matchMedia||!window.matchMedia('(prefers-reduced-motion: reduce)').matches)celebration.classList.add('gmrc-pocket-dice__celebration--animated');result.append(celebration);}
   const entry=el('li','',descriptor+(mod>=0?' + ':' − ')+Math.abs(mod)+' → '+total+' ('+values.join(', ')+')');history.prepend(entry);while(history.children.length>6)history.lastElementChild.remove();
  }catch(error){result.textContent=error.message||'Unable to roll dice.';}
 }
 roll.addEventListener('click',()=>{const n=Number(count.value),mod=Number(modifier.value);if(!count.validity.valid||!modifier.validity.valid){result.textContent='Enter 1–20 dice and a whole-number modifier from −999 to +999.';return;}performRoll(sides,n,mod);});
 // Ability checks share the exact same secure roller, result display, and six-roll history.
 // Weapon damage uses the canonical attack presentation; double dice only on criticals.
 tray.rollDamage=(label,formula,mod,type)=>{
  const match=/^(\d+)d(4|6|8|10|12|20|100)$/i.exec(String(formula||'').trim());
  if(!match||!Number.isSafeInteger(mod)){result.textContent='This damage formula cannot be rolled automatically.';tray.open=true;return;}
  tray.open=true;performRoll(Number(match[2]),Number(match[1]),mod,label+' · '+formula+' '+type);
  result.scrollIntoView({block:'nearest',behavior:'auto'});
 };
 tray.rollCheck=(label,mod)=>{tray.open=true;performRoll(20,1,mod,label+' · 1d20');result.scrollIntoView({block:'nearest',behavior:'auto'});};
 tray.rollAbility=(name,mod)=>{tray.open=true;performRoll(20,1,mod,name+' check · 1d20');result.scrollIntoView({block:'nearest',behavior:'auto'});};
 return tray;
}
const pocketSkillAbilities={'acrobatics':'DEX','animal-handling':'WIS','arcana':'INT','athletics':'STR','deception':'CHA','history':'INT','insight':'WIS','intimidation':'CHA','investigation':'INT','medicine':'WIS','nature':'INT','perception':'WIS','performance':'CHA','persuasion':'CHA','religion':'INT','sleight-of-hand':'DEX','stealth':'DEX','survival':'WIS'};
function pocketTraining(title,entries,diceTray,isSkill){
 const panel=el('details','gmrc-pocket-training'),summary=el('summary','',title),list=el('div','gmrc-pocket-training__list');panel.append(summary,list);
 for(const [key,data] of Object.entries(entries||{})){
  if(!data||!Number.isSafeInteger(data.modifier))continue;
  const name=isSkill?key.split('-').map(word=>word.charAt(0).toUpperCase()+word.slice(1)).join(' '):key;
  const status=data.expertise?'Expertise':data.proficient?'Proficient':'Not proficient';
  const row=el('div','gmrc-pocket-training__row'),label=el('div','gmrc-pocket-training__name');
  label.append(el('strong','',name+' '+(data.modifier>=0?'+':'')+data.modifier),el('small','',(isSkill?(pocketSkillAbilities[key]||'')+' · ':'')+status));
  const button=el('button','gmrc-pocket-training__roll','Roll');button.type='button';button.setAttribute('aria-label','Roll '+name+(isSkill?' skill check':' saving throw'));
  button.addEventListener('click',()=>diceTray.rollCheck(name+(isSkill?' skill check':' saving throw'),data.modifier));row.append(label,button);list.append(row);
 }
 return panel;
}
function pocketArsenal(character,diceTray){
 const panel=el('details','gmrc-pocket-training'),summary=el('summary','','The Adventurer’s Arsenal'),body=el('div','gmrc-pocket-training__list');panel.append(summary,body);
 const attacks=Array.isArray(character.attacks)?character.attacks:[];
 body.append(el('h4','','Equipped attacks'));
 if(!attacks.length)body.append(el('p','','No equipped weapon attacks.'));
 for(const attack of attacks){
  const row=el('div','gmrc-pocket-training__row'),info=el('div','gmrc-pocket-training__name');
  const bonus=Number(attack.attack_bonus),damageMod=Number(attack.damage_modifier);
  info.append(el('strong','',String(attack.label||'Weapon')),el('small','', 'Attack '+(bonus>=0?'+':'')+bonus+' · '+attack.damage_die+(damageMod>=0?' + ':' − ')+Math.abs(damageMod)+' '+String(attack.damage_type||'')));
  row.append(info);
  if(Number.isSafeInteger(bonus)){
   const roll=el('button','gmrc-pocket-training__roll','Attack');roll.type='button';roll.addEventListener('click',()=>diceTray.rollCheck(String(attack.label||'Weapon')+' attack',bonus));row.append(roll);
  }
  for(const [label,formula] of [['Damage',attack.damage_die],['Critical',attack.critical_damage_die]]){
   const button=el('button','gmrc-pocket-training__roll',label);button.type='button';button.addEventListener('click',()=>diceTray.rollDamage(String(attack.label||'Weapon')+' '+label.toLowerCase(),formula,damageMod,String(attack.damage_type||'')));row.append(button);
  }
  body.append(row);
 }
 const gear=el('details','gmrc-pocket-training'),gearSummary=el('summary','','Equipment · read only'),gearList=el('div','gmrc-pocket-training__list');gear.append(gearSummary,gearList);
 const equipment=Array.isArray(character.equipment)?character.equipment:[];
 if(!equipment.length)gearList.append(el('p','','No equipment recorded.'));
 for(const item of equipment){const row=el('div','gmrc-pocket-training__row');row.append(el('strong','',String(item.label||'Item')),el('small','', '×'+String(item.quantity??1)+(item.equipped?' · Equipped':'')));gearList.append(row);}
 body.append(gear);return panel;
}
function pocketSpellbook(character,diceTray){
 const panel=el('details','gmrc-pocket-training'),summary=el('summary','','The Pocket Spellbook'),body=el('div','gmrc-pocket-training__list');panel.append(summary,body);
 const casting=character.spellcasting||{};
 if(casting.ability){
  const measures=el('div','gmrc-pocket-training__list');
  for(const [label,value] of [['Spellcasting ability',casting.ability],['Spell attack bonus',casting.attack_bonus==null?null:(Number(casting.attack_bonus)>=0?'+':'')+casting.attack_bonus],['Spell save DC',casting.save_dc]]){
   if(value!==null&&value!==undefined)measures.append(el('p','',label+': '+String(value)));
  }
  if(Number.isSafeInteger(casting.attack_bonus)){
   const attack=el('button','gmrc-pocket-training__roll','Roll spell attack');attack.type='button';
   attack.addEventListener('click',()=>diceTray.rollAbility('Spell attack',casting.attack_bonus));measures.append(attack);
  }
  body.append(measures);
 }
 const slots=Array.isArray(casting.slots)?casting.slots:[];
 if(slots.length){
  const heading=el('h4','','Spell slots');body.append(heading);
  for(const slot of slots){
   const row=el('div','gmrc-pocket-training__row'),balance=el('span','', 'Level '+slot.level+': '+slot.remaining+' / '+slot.total+' remaining ('+slot.expended+' expended)');
   const use=el('button','gmrc-pocket-training__roll','Use slot'),restore=el('button','gmrc-pocket-training__roll','Restore slot'),message=el('small');
   use.type=restore.type='button';use.disabled=slot.remaining<=0;restore.disabled=slot.expended<=0;
   async function update(action){use.disabled=restore.disabled=true;message.textContent='Saving…';try{await changeSpellSlot(character,slot,action);const updated=character.spellcasting.slots.find(entry=>entry.level===slot.level);Object.assign(slot,updated);balance.textContent='Level '+slot.level+': '+slot.remaining+' / '+slot.total+' remaining ('+slot.expended+' expended)';message.textContent='Saved.';use.disabled=slot.remaining<=0;restore.disabled=slot.expended<=0;}catch(error){message.textContent=error.message+' Refresh characters to retry.';}}
   use.addEventListener('click',()=>update('spend'));restore.addEventListener('click',()=>update('recover'));
   row.append(balance,use,restore,message);body.append(row);
  }
 }
 const spells=Array.isArray(character.spellbook)?character.spellbook:[];
 if(!spells.length){body.append(el('p','','No spells recorded for this adventurer.'));return panel;}
 const ordered=[...spells].sort((a,b)=>(a.level??(a.group==='cantrips'?0:99))-(b.level??(b.group==='cantrips'?0:99))||String(a.name).localeCompare(String(b.name)));
 for(const spell of ordered){
  const entry=el('details','gmrc-pocket-training'),heading=el('summary','',String(spell.name||'Unknown spell')+' · '+(spell.level===0||spell.group==='cantrips'?'Cantrip':Number.isInteger(spell.level)?'Level '+spell.level:'Level not recorded'));
  const content=el('div','gmrc-pocket-training__list');entry.append(heading,content);
  for(const [label,value] of [['School',spell.school],['Casting time',spell.casting_time],['Range',spell.range],['Components',spell.components],['Duration',spell.duration],['Description',spell.rules_text],['At higher levels',spell.higher_levels]]){
   if(value)content.append(el('p','',label+': '+String(value)));
  }
  if(!spell.resolved)content.append(el('p','','This spell has no matching shared-register entry; its mechanics are not available here.'));
  // Only explicit, simple, modifier-free damage/healing formulae are safely auto-rollable.
  // Slot scaling, multi-target effects and spellcasting modifiers need a separate rules-aware phase.
  const formula=String(spell.formula||'').trim(),match=/^(\d+)d(4|6|8|10|12|20|100)$/i.exec(formula);
  if(spell.resolved&&match&&Number(match[1])>=1&&Number(match[1])<=20&&!spell.add_casting_modifier&&['damage','healing'].includes(String(spell.roll_kind||'').toLowerCase())){
   const button=el('button','gmrc-pocket-training__roll','Roll '+String(spell.roll_kind));button.type='button';
   button.addEventListener('click',()=>diceTray.rollDamage(String(spell.name||'Spell')+' '+spell.roll_kind,formula,0,String(spell.damage_type||'')));
   content.append(button);
  }else if(spell.formula)content.append(el('p','','Automatic rolling is not available for this spell formula. Use Guild Diceworks manually as directed by your GM.'));
  body.append(entry);
 }
 body.append(el('p','','Use or restore slots explicitly. Casting or rolling a spell does not automatically expend a slot.')); 
 return panel;
}
function card(character){const article=el('article','gmrc-pocket-character');const head=el('div','gmrc-pocket-character__head');head.append(el('span','gmrc-pocket-character__label','Adventurer'),el('h3','',character.name||'Unnamed adventurer'));article.append(head);const body=el('div','gmrc-pocket-character__body');const hp=character.hp||{};const current=Number(hp.current),maximum=Number(hp.maximum),temporary=Number(hp.temporary);const safeMax=Number.isFinite(maximum)&&maximum>0?maximum:0;const safeCurrent=Number.isFinite(current)?current:0;const safeTemp=Number.isFinite(temporary)?temporary:0;const tiles=el('div','gmrc-pocket-hp');for(const [label,value] of [['Hit points',String(safeCurrent)+' / '+String(safeMax||'—')],['Temporary HP',String(safeTemp)]]){const tile=el('div','gmrc-pocket-hp__tile');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','gmrc-pocket-hp__value',value));tiles.append(tile);}body.append(tiles);const meter=el('div','gmrc-pocket-hp__meter');meter.setAttribute('role','progressbar');meter.setAttribute('aria-label','Current hit points');meter.setAttribute('aria-valuemin','0');meter.setAttribute('aria-valuemax',String(safeMax));meter.setAttribute('aria-valuenow',String(Math.max(0,Math.min(safeMax,safeCurrent))));const fill=el('span','gmrc-pocket-hp__fill');fill.style.width=(safeMax?Math.max(0,Math.min(100,safeCurrent/safeMax*100)):0)+'%';meter.append(fill);body.append(meter);const open=el('button','gmrc-pocket-button gmrc-pocket-character__open','Open character sheet');open.type='button';open.addEventListener('click',()=>showDetail(character));body.append(open);article.append(body);return article;}
// Keep one live instance of each control: moving nodes preserves HP, dice and spell-slot handlers.
function pocketDashboard(detail,character,back,diceTray){
 const children=Array.from(detail.children),portrait=detail.querySelector('.gmrc-pocket-detail__portrait, .gmrc-pocket-detail__portrait-fallback');
 const heading=detail.querySelector('.gmrc-pocket-detail__heading'),subtitle=detail.querySelector('.gmrc-pocket-detail__subtitle'),stats=detail.querySelector('.gmrc-pocket-detail__stats');
 const vitality=detail.querySelector('.gmrc-pocket-vitality'),abilities=detail.querySelector('.gmrc-pocket-detail__abilities');
 const training=children.filter(node=>node.classList.contains('gmrc-pocket-training'));
 const saving=training.find(node=>node.querySelector('summary')?.textContent==='Saving throws');
 const skills=training.find(node=>node.querySelector('summary')?.textContent==='Skills');
 const arsenal=training.find(node=>node.querySelector('summary')?.textContent==='The Adventurer’s Arsenal');
 const spellbook=training.find(node=>node.querySelector('summary')?.textContent==='The Pocket Spellbook');
 const abilityHeading=children.find(node=>node.tagName==='H4'&&node.textContent==='Ability scores');
 const shell=el('div','gmrc-pocket-dashboard'),hero=el('section','gmrc-pocket-dashboard__hero');
 hero.setAttribute('aria-label','Adventurer overview');
 for(const node of [portrait,heading,subtitle,stats])if(node)hero.append(node);
 const tabs=el('nav','gmrc-pocket-dashboard__tabs');tabs.setAttribute('aria-label','Pocket character navigation');
 const sections=[['overview','Overview',[vitality]],['character','Character',[abilityHeading,abilities,saving,skills]],['combat','Combat',[arsenal]],['spells','Spellbook',[spellbook]],['equipment','Equipment',[]]];
 const panels=new Map(),buttons=new Map();
 for(const [key,label,nodes] of sections){
  const button=el('button','gmrc-pocket-dashboard__tab',label);button.type='button';button.id=root.id+'-tab-'+key;button.setAttribute('aria-controls',root.id+'-panel-'+key);button.setAttribute('aria-current',key==='overview'?'page':'false');
  const panel=el('section','gmrc-pocket-dashboard__panel');panel.id=root.id+'-panel-'+key;panel.setAttribute('aria-labelledby',button.id);panel.hidden=key!=='overview';
  for(const node of nodes)if(node)panel.append(node);
  tabs.append(button);panels.set(key,panel);buttons.set(key,button);
 }
 const equipment=panels.get('equipment');const gear=arsenal?.querySelector('details');
 // The equipment display is read-only and shares its original DOM with the Arsenal.
 // Move it to its own panel without cloning or creating duplicate event handlers.
 if(gear)equipment.append(gear);
 else equipment.append(el('p','','No equipment recorded.'));
 function activate(key,focus=false){for(const [name,panel] of panels){const active=name===key;panel.hidden=!active;const button=buttons.get(name);button.setAttribute('aria-current',active?'page':'false');}if(focus)buttons.get(key).focus();}
 const more=el('button','gmrc-pocket-dashboard__tab gmrc-pocket-dashboard__more','More');more.type='button';more.setAttribute('aria-expanded','false');more.setAttribute('aria-controls',root.id+'-more-menu');
 const moreMenu=el('div','gmrc-pocket-dashboard__more-menu');moreMenu.id=root.id+'-more-menu';moreMenu.hidden=true;
 const equipmentButton=buttons.get('equipment');equipmentButton.remove();moreMenu.append(equipmentButton);tabs.append(more);
 function closeMore(){moreMenu.hidden=true;more.setAttribute('aria-expanded','false');}
 more.addEventListener('click',()=>{const opening=moreMenu.hidden;if(opening)diceTray.open=false;moreMenu.hidden=!opening;more.setAttribute('aria-expanded',opening?'true':'false');});
 for(const [key,button] of buttons){button.addEventListener('click',()=>{activate(key);closeMore();});}
 document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!moreMenu.hidden){closeMore();more.focus();}});
 document.addEventListener('click',event=>{if(!moreMenu.hidden&&!tabs.contains(event.target)&&!moreMenu.contains(event.target))closeMore();});
 // The original Diceworks node remains mounted once, outside the switching panels.
 // Quick rolls update its collapsed summary and expand the tray without switching tabs.
 const dock=el('aside','gmrc-pocket-dice-dock');dock.setAttribute('aria-label','Persistent Guild Diceworks');dock.append(diceTray);
 const bottomDock=el('div','gmrc-pocket-bottom-dock');bottomDock.setAttribute('aria-label','Pocket controls');bottomDock.append(dock,moreMenu,tabs);const viewport=el('div','gmrc-pocket-dashboard__viewport');viewport.append(back,hero,...panels.values());shell.append(viewport,bottomDock);detail.replaceChildren(shell);root.classList.add('gmrc-pocket--character-open');
}
function showDetail(character){selectedCharacter=character;characterNav.disabled=false;setPocketView('character');detail.replaceChildren();const back=el('button','gmrc-pocket-button','← Back to characters');back.type='button';back.addEventListener('click',()=>{setPocketView('home');root.classList.remove('gmrc-pocket--character-open');detail.hidden=true;list.replaceChildren();for(const entry of currentCharacters)list.append(card(entry));list.hidden=false;refresh.hidden=false;refresh.focus();root.querySelector('.gmrc-pocket-status').textContent='Choose an adventurer.';});detail.append(back);const portrait=character.portrait||{};if((portrait.kind==='image'||portrait.kind==='svg')&&typeof portrait.url==='string'&&(portrait.url.startsWith('https://')||portrait.url.startsWith('http://')||portrait.url.startsWith('data:image/svg+xml;base64,'))){const img=el('img','gmrc-pocket-detail__portrait');img.src=portrait.url;img.alt='Portrait of '+(character.name||'adventurer');img.loading='lazy';img.decoding='async';detail.append(img);}else{detail.append(el('p','gmrc-pocket-detail__portrait-fallback','Portrait not available'));}detail.append(el('h3','gmrc-pocket-detail__heading',character.name||'Unnamed adventurer'),el('p','gmrc-pocket-detail__subtitle',[character.race,character.class,character.level?'Level '+character.level:''].filter(Boolean).join(' · ')));const stats=el('div','gmrc-pocket-detail__stats');const hp=character.hp||{};for(const [label,value] of [['HP',String(hp.current??'—')+' / '+String(hp.maximum??'—')],['Temp HP',hp.temporary??0],['Armour class',character.armour_class??'—'],['Initiative',character.initiative??'—'],['Speed',character.speed_feet!=null?character.speed_feet+' ft':'—']]){const tile=el('div','gmrc-pocket-detail__stat');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','',value));stats.append(tile);}detail.append(stats,vitalityControls(character),el('h4','','Ability scores'));const abilities=el('div','gmrc-pocket-detail__abilities'),diceTray=pocketDiceworks();for(const [label,score] of Object.entries(character.abilities||{})){const n=Number(score);const mod=Number.isFinite(n)?Math.floor((n-10)/2):null;const tile=el('div','gmrc-pocket-detail__ability');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','',score),el('small','',mod===null?'':(mod>=0?'+':'')+mod));if(mod!==null&&Number.isSafeInteger(mod)){const quick=el('button','gmrc-pocket-ability-roll','Roll '+label);quick.type='button';quick.setAttribute('aria-label','Roll '+label+' ability check with modifier '+(mod>=0?'+':'')+mod);quick.addEventListener('click',()=>diceTray.rollAbility(label,mod));tile.append(quick);}abilities.append(tile);}detail.append(abilities,diceTray);detail.insertBefore(pocketTraining('Saving throws',character.saving_throws,diceTray,false),diceTray);detail.insertBefore(pocketTraining('Skills',character.skills,diceTray,true),diceTray);detail.insertBefore(pocketArsenal(character,diceTray),diceTray);detail.insertBefore(pocketSpellbook(character,diceTray),diceTray);pocketDashboard(detail,character,back,diceTray);list.hidden=true;refresh.hidden=true;detail.hidden=false;detail.scrollIntoView({block:'start',behavior:'auto'});back.focus({preventScroll:true});}
async function load(){root.classList.remove('gmrc-pocket--character-open');selectedCharacter=null;characterNav.disabled=true;setPocketView('home');refresh.disabled=true;status.textContent='Loading your characters…';list.replaceChildren();detail.hidden=true;detail.replaceChildren();list.hidden=false;refresh.hidden=false;try{const session=await request(config.session);welcome.textContent='Welcome, '+session.user.display_name;const data=await request(config.characters);const characters=Array.isArray(data.characters)?data.characters:[];currentCharacters=characters;for(const character of characters)list.append(card(character));status.textContent=characters.length?characters.length+' character(s) in your ledger.':'No characters found in your ledger yet.';}catch(error){status.textContent=error.message||'Unable to load characters.';}finally{refresh.disabled=false;}}
homeNav.addEventListener('click',()=>{if(!detail.hidden){const back=detail.querySelector('button');if(back)back.click();}else{setPocketView('home');list.focus?.();}});characterNav.addEventListener('click',()=>{if(selectedCharacter)showDetail(selectedCharacter);});refresh.addEventListener('click',load);load();
})();
</script>
JS;
        $html = str_replace(['POCKET_ROOT_ID', 'POCKET_CONFIG'], [wp_json_encode($id), $json], $html);
        return $html;
    }
}
