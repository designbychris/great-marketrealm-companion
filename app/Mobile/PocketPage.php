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
            'vitalityBase' => esc_url_raw(rest_url('gmrc-pocket/v1/characters/')),
            'nonce' => wp_create_nonce('wp_rest'),
        ];
        $json = wp_json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $html = '<section class="gmrc-pocket" id="' . esc_attr($id) . '" aria-label="Pocket character sheet">'
            . '<header class="gmrc-pocket__header"><span class="gmrc-pocket-kicker">THE GREAT MARKETREALM</span><h2>Pocket Companion</h2>'
            . '<p class="gmrc-pocket-welcome" aria-live="polite">Opening the character ledger…</p></header>'
            . '<div class="gmrc-pocket-status" role="status" aria-live="polite">Loading your characters…</div>'
            . '<div class="gmrc-pocket-characters" aria-label="Your characters"></div>'
            . '<section class="gmrc-pocket-detail" aria-label="Character sheet" hidden></section>'
            . '<button type="button" class="gmrc-pocket-button gmrc-pocket-refresh">Refresh characters</button>'
            . '</section>';
        $html .= <<<'CSS'
<style>
.gmrc-pocket{box-sizing:border-box;max-width:850px;margin:1.25rem auto;padding:clamp(1rem,4vw,2rem);border:2px solid #b48a46;border-radius:20px;background:linear-gradient(145deg,#fff9e9,#eee1c1);color:#30291d;font:inherit;box-shadow:0 10px 30px #241a0e24}.gmrc-pocket *{box-sizing:border-box}.gmrc-pocket__header{border-bottom:2px solid #c9ae79;padding-bottom:.8rem}.gmrc-pocket h2{margin:.2rem 0 .5rem;color:#354a32;font-size:clamp(1.6rem,6vw,2.3rem)}.gmrc-pocket-kicker{font-size:.75rem;letter-spacing:.14em;font-weight:700;color:#52683d}.gmrc-pocket-characters{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,300px),1fr));gap:1rem;margin:1.2rem 0}.gmrc-pocket-character{overflow:hidden;background:#fffaf0;border:2px solid #c5a66c;border-radius:15px;box-shadow:0 4px 12px #3a2c161c}.gmrc-pocket-character__head{padding:1rem;background:#e6ebda;border-bottom:1px solid #c5a66c}.gmrc-pocket-character h3{margin:0;color:#354a32;font-size:clamp(1.2rem,5vw,1.55rem);overflow-wrap:anywhere}.gmrc-pocket-character__body{padding:1rem}.gmrc-pocket-character__label{display:block;font-size:.72rem;letter-spacing:.09em;text-transform:uppercase;font-weight:800;color:#52683d}.gmrc-pocket-hp{display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin:.8rem 0}.gmrc-pocket-hp__tile{padding:.8rem;border:1px solid #d0be97;border-radius:10px;background:#fff}.gmrc-pocket-hp__value{display:block;font-size:1.45rem;font-weight:800;color:#354a32;font-variant-numeric:tabular-nums}.gmrc-pocket-hp__meter{height:9px;overflow:hidden;background:#d9d4c5;border-radius:99px}.gmrc-pocket-hp__fill{display:block;height:100%;background:#628344;border-radius:99px;max-width:100%}.gmrc-pocket-character__note{margin:.85rem 0 0;font-size:.87rem;color:#625943}.gmrc-pocket-button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:.75rem 1rem;border:0;border-radius:9px;background:#354a32;color:#fff!important;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.gmrc-pocket-button:focus-visible{outline:3px solid #a36a16;outline-offset:3px}.gmrc-pocket-status{margin-top:1rem}.gmrc-pocket-status:empty{display:none}@media(max-width:420px){.gmrc-pocket{margin:.5rem auto;padding:1rem;border-radius:13px}.gmrc-pocket-characters{grid-template-columns:minmax(0,1fr)}.gmrc-pocket-hp__value{font-size:1.25rem}}
 .gmrc-pocket-detail[hidden],.gmrc-pocket-characters[hidden]{display:none!important}.gmrc-pocket-detail{margin:1.2rem 0;padding:1rem;border:2px solid #b48a46;border-radius:15px;background:#fffaf0}.gmrc-pocket-detail__portrait{display:block;width:min(100%,220px);aspect-ratio:4/5;object-fit:contain;margin:1rem auto;border:2px solid #b48a46;border-radius:12px;background:#eee1c1}.gmrc-pocket-detail__portrait-fallback{text-align:center;padding:1.5rem;color:#52683d}.gmrc-pocket-detail__heading{margin:.5rem 0;color:#354a32}.gmrc-pocket-detail__subtitle{color:#52683d}.gmrc-pocket-detail__stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem;margin:1rem 0}.gmrc-pocket-detail__stat{padding:.75rem .35rem;border:1px solid #c5a66c;border-radius:9px;text-align:center;background:#fff}.gmrc-pocket-detail__stat strong{display:block;font-size:1.25rem;color:#354a32}.gmrc-pocket-detail__abilities{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem}.gmrc-pocket-detail__ability{padding:.65rem .2rem;text-align:center;border:1px solid #c5a66c;border-radius:9px;background:#f1f3e7}.gmrc-pocket-detail__ability strong{display:block;font-size:1.2rem}.gmrc-pocket-detail__ability small{display:block}.gmrc-pocket-vitality{display:grid;gap:.65rem;padding:1rem;margin:1rem 0;border:1px solid #c5a66c;border-radius:10px;background:#f1f3e7}.gmrc-pocket-vitality label{font-weight:700}.gmrc-pocket-vitality input{width:100%;min-height:44px;padding:.5rem;border:1px solid #b48a46;border-radius:7px;background:#fff;color:#30291d;font:inherit}.gmrc-pocket-vitality__actions{display:grid;grid-template-columns:1fr 1fr;gap:.6rem}.gmrc-pocket-vitality__quick{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.6rem}.gmrc-pocket-vitality__quick button{width:100%}.gmrc-pocket-vitality__amount{max-width:100%}.gmrc-pocket-vitality__message{min-height:1.4em}.gmrc-pocket-character__open{width:100%;margin-top:1rem}@media(max-width:360px){.gmrc-pocket-detail__stats,.gmrc-pocket-detail__abilities{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
CSS;
        $html .= <<<'JS'
<script>
(function(){"use strict";
const root=document.getElementById(POCKET_ROOT_ID);if(!root)return;
const config=POCKET_CONFIG;
let currentCharacters=[];const status=root.querySelector('.gmrc-pocket-status'),welcome=root.querySelector('.gmrc-pocket-welcome'),list=root.querySelector('.gmrc-pocket-characters'),refresh=root.querySelector('.gmrc-pocket-refresh'),detail=root.querySelector('.gmrc-pocket-detail');
function el(tag,className,value){const node=document.createElement(tag);if(className)node.className=className;if(value!==undefined)node.textContent=String(value);return node;}
async function request(url){const response=await fetch(url,{credentials:'same-origin',headers:{'X-WP-Nonce':config.nonce,'Accept':'application/json'},cache:'no-store'});if(!response.ok)throw new Error(response.status===401||response.status===403?'Your session has expired. Please sign in again.':'Could not reach the character ledger. Please try again.');return response.json();}
async function saveVitality(character,current,temporary){const hp=character.hp||{};const response=await fetch(config.vitalityBase+encodeURIComponent(character.id)+'/vitality',{method:'POST',credentials:'same-origin',cache:'no-store',headers:{'X-WP-Nonce':config.nonce,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({current,temporary,expected_current:hp.current,expected_temporary:hp.temporary})});const data=await response.json().catch(()=>({}));if(!response.ok)throw new Error(data.message||'Unable to save HP. Please refresh and try again.');character.hp=data.hp;return data.hp;}
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
function card(character){const article=el('article','gmrc-pocket-character');const head=el('div','gmrc-pocket-character__head');head.append(el('span','gmrc-pocket-character__label','Adventurer'),el('h3','',character.name||'Unnamed adventurer'));article.append(head);const body=el('div','gmrc-pocket-character__body');const hp=character.hp||{};const current=Number(hp.current),maximum=Number(hp.maximum),temporary=Number(hp.temporary);const safeMax=Number.isFinite(maximum)&&maximum>0?maximum:0;const safeCurrent=Number.isFinite(current)?current:0;const safeTemp=Number.isFinite(temporary)?temporary:0;const tiles=el('div','gmrc-pocket-hp');for(const [label,value] of [['Hit points',String(safeCurrent)+' / '+String(safeMax||'—')],['Temporary HP',String(safeTemp)]]){const tile=el('div','gmrc-pocket-hp__tile');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','gmrc-pocket-hp__value',value));tiles.append(tile);}body.append(tiles);const meter=el('div','gmrc-pocket-hp__meter');meter.setAttribute('role','progressbar');meter.setAttribute('aria-label','Current hit points');meter.setAttribute('aria-valuemin','0');meter.setAttribute('aria-valuemax',String(safeMax));meter.setAttribute('aria-valuenow',String(Math.max(0,Math.min(safeMax,safeCurrent))));const fill=el('span','gmrc-pocket-hp__fill');fill.style.width=(safeMax?Math.max(0,Math.min(100,safeCurrent/safeMax*100)):0)+'%';meter.append(fill);body.append(meter);const open=el('button','gmrc-pocket-button gmrc-pocket-character__open','Open character sheet');open.type='button';open.addEventListener('click',()=>showDetail(character));body.append(open);article.append(body);return article;}
function showDetail(character){detail.replaceChildren();const back=el('button','gmrc-pocket-button','← Back to characters');back.type='button';back.addEventListener('click',()=>{detail.hidden=true;list.replaceChildren();for(const entry of currentCharacters)list.append(card(entry));list.hidden=false;refresh.hidden=false;refresh.focus();root.querySelector('.gmrc-pocket-status').textContent='Choose an adventurer.';});detail.append(back);const portrait=character.portrait||{};if((portrait.kind==='image'||portrait.kind==='svg')&&typeof portrait.url==='string'&&(portrait.url.startsWith('https://')||portrait.url.startsWith('http://')||portrait.url.startsWith('data:image/svg+xml;base64,'))){const img=el('img','gmrc-pocket-detail__portrait');img.src=portrait.url;img.alt='Portrait of '+(character.name||'adventurer');img.loading='lazy';img.decoding='async';detail.append(img);}else{detail.append(el('p','gmrc-pocket-detail__portrait-fallback','Portrait not available'));}detail.append(el('h3','gmrc-pocket-detail__heading',character.name||'Unnamed adventurer'),el('p','gmrc-pocket-detail__subtitle',[character.race,character.class,character.level?'Level '+character.level:''].filter(Boolean).join(' · ')));const stats=el('div','gmrc-pocket-detail__stats');const hp=character.hp||{};for(const [label,value] of [['HP',String(hp.current??'—')+' / '+String(hp.maximum??'—')],['Temp HP',hp.temporary??0],['Armour class',character.armour_class??'—'],['Initiative',character.initiative??'—'],['Speed',character.speed_feet!=null?character.speed_feet+' ft':'—']]){const tile=el('div','gmrc-pocket-detail__stat');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','',value));stats.append(tile);}detail.append(stats,vitalityControls(character),el('h4','','Ability scores'));const abilities=el('div','gmrc-pocket-detail__abilities');for(const [label,score] of Object.entries(character.abilities||{})){const n=Number(score);const mod=Number.isFinite(n)?Math.floor((n-10)/2):null;const tile=el('div','gmrc-pocket-detail__ability');tile.append(el('span','gmrc-pocket-character__label',label),el('strong','',score),el('small','',mod===null?'':(mod>=0?'+':'')+mod));abilities.append(tile);}detail.append(abilities,el('p','gmrc-pocket-character__note','Guild Diceworks will follow in a later phase.'));list.hidden=true;refresh.hidden=true;detail.hidden=false;detail.scrollIntoView({block:'start',behavior:'auto'});back.focus({preventScroll:true});}
async function load(){refresh.disabled=true;status.textContent='Loading your characters…';list.replaceChildren();detail.hidden=true;detail.replaceChildren();list.hidden=false;refresh.hidden=false;try{const session=await request(config.session);welcome.textContent='Welcome, '+session.user.display_name;const data=await request(config.characters);const characters=Array.isArray(data.characters)?data.characters:[];currentCharacters=characters;for(const character of characters)list.append(card(character));status.textContent=characters.length?characters.length+' character(s) in your ledger.':'No characters found in your ledger yet.';}catch(error){status.textContent=error.message||'Unable to load characters.';}finally{refresh.disabled=false;}}
refresh.addEventListener('click',load);load();
})();
</script>
JS;
        $html = str_replace(['POCKET_ROOT_ID', 'POCKET_CONFIG'], [wp_json_encode($id), $json], $html);
        return $html;
    }
}
