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
        $html = '<section class="gmrc-pocket" id="' . esc_attr($id) . '" aria-label="Pocket character sheet">'
            . '<header class="gmrc-pocket__header"><span class="gmrc-pocket-kicker">THE GREAT MARKETREALM</span><h2>Pocket Companion</h2>'
            . '<p class="gmrc-pocket-welcome" aria-live="polite">Opening the character ledger…</p></header>'
            . '<div class="gmrc-pocket-status" role="status" aria-live="polite">Loading your characters…</div>'
            . '<div class="gmrc-pocket-characters" aria-label="Your characters"></div>'
            . '<button type="button" class="gmrc-pocket-button gmrc-pocket-refresh">Refresh characters</button>'
            . '</section>';
        $html .= <<<'CSS'
<style>
.gmrc-pocket{box-sizing:border-box;max-width:850px;margin:1.25rem auto;padding:clamp(1rem,4vw,2rem);border:2px solid #b48a46;border-radius:20px;background:linear-gradient(145deg,#fff9e9,#eee1c1);color:#30291d;font:inherit;box-shadow:0 10px 30px #241a0e24}.gmrc-pocket *{box-sizing:border-box}.gmrc-pocket__header{border-bottom:2px solid #c9ae79;padding-bottom:.8rem}.gmrc-pocket h2{margin:.2rem 0 .5rem;color:#354a32;font-size:clamp(1.6rem,6vw,2.3rem)}.gmrc-pocket-kicker{font-size:.75rem;letter-spacing:.14em;font-weight:700;color:#52683d}.gmrc-pocket-characters{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,300px),1fr));gap:1rem;margin:1.2rem 0}.gmrc-pocket-character{overflow:hidden;background:#fffaf0;border:2px solid #c5a66c;border-radius:15px;box-shadow:0 4px 12px #3a2c161c}.gmrc-pocket-character__head{padding:1rem;background:#e6ebda;border-bottom:1px solid #c5a66c}.gmrc-pocket-character h3{margin:0;color:#354a32;font-size:clamp(1.2rem,5vw,1.55rem);overflow-wrap:anywhere}.gmrc-pocket-character__body{padding:1rem}.gmrc-pocket-character__label{display:block;font-size:.72rem;letter-spacing:.09em;text-transform:uppercase;font-weight:800;color:#52683d}.gmrc-pocket-hp{display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin:.8rem 0}.gmrc-pocket-hp__tile{padding:.8rem;border:1px solid #d0be97;border-radius:10px;background:#fff}.gmrc-pocket-hp__value{display:block;font-size:1.45rem;font-weight:800;color:#354a32;font-variant-numeric:tabular-nums}.gmrc-pocket-hp__meter{height:9px;overflow:hidden;background:#d9d4c5;border-radius:99px}.gmrc-pocket-hp__fill{display:block;height:100%;background:#628344;border-radius:99px;max-width:100%}.gmrc-pocket-character__note{margin:.85rem 0 0;font-size:.87rem;color:#625943}.gmrc-pocket-button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:.75rem 1rem;border:0;border-radius:9px;background:#354a32;color:#fff!important;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.gmrc-pocket-button:focus-visible{outline:3px solid #a36a16;outline-offset:3px}.gmrc-pocket-status{margin-top:1rem}.gmrc-pocket-status:empty{display:none}@media(max-width:420px){.gmrc-pocket{margin:.5rem auto;padding:1rem;border-radius:13px}.gmrc-pocket-characters{grid-template-columns:minmax(0,1fr)}.gmrc-pocket-hp__value{font-size:1.25rem}}
</style>
CSS;
        $html .= <<<'JS'
<script>
(function(){"use strict";
const root=document.getElementById(POCKET_ROOT_ID);if(!root)return;
const config=POCKET_CONFIG;
const status=root.querySelector('.gmrc-pocket-status'),welcome=root.querySelector('.gmrc-pocket-welcome'),list=root.querySelector('.gmrc-pocket-characters'),refresh=root.querySelector('.gmrc-pocket-refresh');
function el(tag,className,value){const node=document.createElement(tag);if(className)node.className=className;if(value!==undefined)node.textContent=String(value);return node;}
async function request(url){const response=await fetch(url,{credentials:'same-origin',headers:{'X-WP-Nonce':config.nonce,'Accept':'application/json'},cache:'no-store'});if(!response.ok)throw new Error(response.status===401||response.status===403?'Your session has expired. Please sign in again.':'Could not reach the character ledger. Please try again.');return response.json();}
function card(character){const article=el('article','gmrc-pocket-character');const head=el('div','gmrc-pocket-character__head');head.append(el('span','gmrc-pocket-character__label','Adventurer'),el('h3','',character.name||'Unnamed adventurer'));article.append(head);const body=el('div','gmrc-pocket-character__body');const hp=character.hp||{};const current=Number(hp.current),maximum=Number(hp.maximum),temporary=Number(hp.temporary);const safeMax=Number.isFinite(maximum)&&maximum>0?maximum:0;const safeCurrent=Number.isFinite(current)?current:0;const safeTemp=Number.isFinite(temporary)?temporary:0;const tiles=el('div','gmrc-pocket-hp');for(const [label,value] of [['Hit points',String(safeCurrent)+' / '+String(safeMax||'—')],['Temporary HP',String(safeTemp)]]){const tile=el('div','gmrc-pocket-hp__tile');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','gmrc-pocket-hp__value',value));tiles.append(tile);}body.append(tiles);const meter=el('div','gmrc-pocket-hp__meter');meter.setAttribute('role','progressbar');meter.setAttribute('aria-label','Current hit points');meter.setAttribute('aria-valuemin','0');meter.setAttribute('aria-valuemax',String(safeMax));meter.setAttribute('aria-valuenow',String(Math.max(0,Math.min(safeMax,safeCurrent))));const fill=el('span','gmrc-pocket-hp__fill');fill.style.width=(safeMax?Math.max(0,Math.min(100,safeCurrent/safeMax*100)):0)+'%';meter.append(fill);body.append(meter,el('p','gmrc-pocket-character__note','Read-only preview · HP editing and Guild Diceworks are coming in a later phase.'));article.append(body);return article;}
async function load(){refresh.disabled=true;status.textContent='Loading your characters…';list.replaceChildren();try{const session=await request(config.session);welcome.textContent='Welcome, '+session.user.display_name;const data=await request(config.characters);const characters=Array.isArray(data.characters)?data.characters:[];for(const character of characters)list.append(card(character));status.textContent=characters.length?characters.length+' character(s) in your ledger.':'No characters found in your ledger yet.';}catch(error){status.textContent=error.message||'Unable to load characters.';}finally{refresh.disabled=false;}}
refresh.addEventListener('click',load);load();
})();
</script>
JS;
        $html = str_replace(['POCKET_ROOT_ID', 'POCKET_CONFIG'], [wp_json_encode($id), $json], $html);
        return $html;
    }
}
