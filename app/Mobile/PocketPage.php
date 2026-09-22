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
                . '<p class="gmrc-pocket-entry__eyebrow">THE POCKET COMPANION</p>'
                . '<h2 id="gmrc-pocket-entry-title">Welcome, adventurer</h2>'
                . '<p>Enter the Guild Gate with your existing Great Marketrealm account to open your character ledger.</p>'
                . '<a class="gmrc-pocket-entry__button" href="' . esc_url($gateUrl) . '">Sign in at the Guild Gate</a>'
                . '</div></section>'
                . '<style>.gmrc-pocket-entry{min-height:70svh;display:grid;place-items:center;padding:clamp(1rem,4vw,3rem);background:linear-gradient(145deg,#233b29e8,#4b5832dd),radial-gradient(circle at 50% 20%,#b89b55,#34452e);color:#322b1e}.gmrc-pocket-entry *{box-sizing:border-box}.gmrc-pocket-entry__panel{width:min(100%,440px);padding:clamp(1.2rem,5vw,2.5rem);text-align:center;border:3px solid #b68b44;border-radius:18px;background:#fff4d9;box-shadow:0 16px 50px #0005}.gmrc-pocket-entry__logo{display:block;width:min(100%,340px);height:auto;max-height:190px;object-fit:contain;margin:0 auto 1rem}.gmrc-pocket-entry__eyebrow{font-size:.78rem;font-weight:800;letter-spacing:.14em;color:#52683d}.gmrc-pocket-entry h2{font-size:clamp(1.6rem,6vw,2.2rem);color:#354a32;margin:.5rem 0}.gmrc-pocket-entry p{line-height:1.55}.gmrc-pocket-entry__button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;margin-top:1rem;padding:.8rem 1.2rem;border-radius:9px;background:#354a32;color:#fff!important;font-weight:700;text-decoration:none}.gmrc-pocket-entry__button:focus-visible{outline:3px solid #ad6b14;outline-offset:4px}@media(prefers-reduced-motion:reduce){.gmrc-pocket-entry *{scroll-behavior:auto}}</style>';
        }

        $id = 'gmrc-pocket-' . wp_unique_id();
        $config = [
            'session' => esc_url_raw(rest_url('gmrc-pocket/v1/session')),
            'characters' => esc_url_raw(rest_url('gmrc-pocket/v1/characters')),
            'nonce' => wp_create_nonce('wp_rest'),
        ];
        $json = wp_json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $html = '<section class="gmrc-pocket" id="' . esc_attr($id) . '">'
            . '<header><span class="gmrc-pocket-kicker">THE GREAT MARKETREALM</span><h2>Pocket Companion</h2>'
            . '<p class="gmrc-pocket-welcome" aria-live="polite">Opening the character ledger…</p></header>'
            . '<div class="gmrc-pocket-status" role="status" aria-live="polite">Loading your characters…</div>'
            . '<div class="gmrc-pocket-characters" aria-label="Your characters"></div>'
            . '<button type="button" class="gmrc-pocket-button gmrc-pocket-refresh">Refresh characters</button>'
            . '</section>';
        $html .= '<style>.gmrc-pocket{box-sizing:border-box;max-width:780px;margin:1.5rem auto;padding:clamp(1rem,4vw,2rem);border:2px solid #ab8745;border-radius:20px;background:#fff9e9;color:#292b24;font:inherit;box-shadow:0 8px 28px #241a0e1a}.gmrc-pocket *{box-sizing:border-box}.gmrc-pocket h2{margin:.2rem 0 1rem;color:#354a32;font-size:clamp(1.6rem,6vw,2.3rem)}.gmrc-pocket-kicker{font-size:.75rem;letter-spacing:.14em;font-weight:700}.gmrc-pocket-characters{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,230px),1fr));gap:1rem;margin:1.2rem 0}.gmrc-pocket-character{padding:1rem;background:#fff;border:1px solid #cbbd9f;border-radius:12px}.gmrc-pocket-character h3{margin:0 0 .75rem;color:#354a32;overflow-wrap:anywhere}.gmrc-pocket-hp{display:flex;flex-wrap:wrap;gap:.6rem}.gmrc-pocket-hp span{padding:.35rem .55rem;border-radius:7px;background:#e8efdf}.gmrc-pocket-button{display:inline-block;min-height:44px;padding:.75rem 1rem;border:0;border-radius:9px;background:#354a32;color:#fff!important;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.gmrc-pocket-button:focus-visible{outline:3px solid #a36a16;outline-offset:3px}.gmrc-pocket-status{margin-top:1rem}</style>';
        $html .= '<script>(function(){"use strict";const root=document.getElementById(' . wp_json_encode($id) . ');if(!root)return;const config=' . $json . ';const status=root.querySelector(".gmrc-pocket-status"),welcome=root.querySelector(".gmrc-pocket-welcome"),list=root.querySelector(".gmrc-pocket-characters"),refresh=root.querySelector(".gmrc-pocket-refresh");async function request(url){const response=await fetch(url,{credentials:"same-origin",headers:{"X-WP-Nonce":config.nonce,"Accept":"application/json"},cache:"no-store"});if(!response.ok)throw new Error(response.status===401||response.status===403?"Your session has expired. Please sign in again.":"Could not reach the character ledger. Please try again.");return response.json()}function element(tag,className,value){const node=document.createElement(tag);if(className)node.className=className;if(value!==undefined)node.textContent=String(value);return node}async function load(){refresh.disabled=true;status.textContent="Loading your characters…";list.replaceChildren();try{const session=await request(config.session);welcome.textContent="Welcome, "+session.user.display_name;const data=await request(config.characters);const characters=Array.isArray(data.characters)?data.characters:[];for(const character of characters){const card=element("article","gmrc-pocket-character");card.append(element("h3","",character.name));const hp=element("div","gmrc-pocket-hp");const points=character.hp||{};for(const [label,value] of [["HP",String(points.current??"—")+" / "+String(points.maximum??"—")],["Temporary HP",points.temporary??0]])hp.append(element("span","",label+": "+value));card.append(hp);list.append(card)}status.textContent=characters.length?characters.length+" character(s) in your ledger.":"No characters found in your ledger yet."}catch(error){status.textContent=error.message||"Unable to load characters."}finally{refresh.disabled=false}}refresh.addEventListener("click",load);load()})();</script>';
        return $html;
    }
}
