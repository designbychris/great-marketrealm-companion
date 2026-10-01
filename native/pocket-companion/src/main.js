import { App } from '@capacitor/app';
import { Browser } from '@capacitor/browser';
import { Capacitor } from '@capacitor/core';
import { SecureStorage } from '@aparajita/capacitor-secure-storage';

const ORIGIN = 'https://greatmarketrealm.co.uk';
const BEGIN = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/begin`;
const TOKEN = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/token`;
const SESSION = `${ORIGIN}/wp-json/gmrc-pocket/v1/session`;
const REVOKE = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/revoke`;
const CHARACTERS = `${ORIGIN}/wp-json/gmrc-pocket/v1/characters`;
const SPELL_SLOTS = characterId => `${CHARACTERS}/${encodeURIComponent(characterId)}/spell-slots`;
const CALLBACK = 'uk.co.greatmarketrealm.pocket://auth/callback';
const PRIVACY_URL = 'https://greatmarketrealm.co.uk/the-pocket-companion/privacy/';
const DELETE_ACCOUNT_URL = 'https://greatmarketrealm.co.uk/companion/delete-account/';
const SUPPORT_URL = 'https://greatmarketrealm.co.uk/support/';
const TOKEN_KEY = 'access-token';
const STORAGE_PREFIX = 'gmrc_pocket_';

const enter = document.querySelector('#enter-guild');
const leave = document.querySelector('#leave-guild');
const status = document.querySelector('#native-status');
const auby = document.querySelector('#auby-state');
const nativeShell = document.querySelector('.native-shell');
const gateView = document.querySelector('#gate-view');
const registerView = document.querySelector('#register-view');
const characterView = document.querySelector('#character-view');
const openRegister = document.querySelector('#open-register');
const registerBack = document.querySelector('#register-back');
const registerRefresh = document.querySelector('#register-refresh');
const registerStatus = document.querySelector('#register-status');
const characterList = document.querySelector('#character-list');
const characterBack = document.querySelector('#character-back');
const characterTitle = document.querySelector('#character-title');
const characterLedger = document.querySelector('#character-ledger');
const connectionBanner = document.querySelector('#connection-banner');
const gatePrivacy = document.querySelector('#gate-privacy');
const gateSupport = document.querySelector('#gate-support');

let pending = null;
let accessToken = null;
let liveCharacters = [];
let selectedCharacterId = null;
let lastSessionValidation = 0;
let resumeValidationInFlight = false;
let registerLoading = false;
let characterRefreshLoading = false;
const RESUME_REVALIDATE_AFTER_MS = 60 * 1000;

const setStatus = (message, kind = '') => {
  status.textContent = message;
  status.className = `status ${kind}`.trim();
};

const setAuby = state => {
  const success = state === 'success';
  auby.src = success ? '/auby-success.png' : '/auby-pocket.png';
  auby.alt = success
    ? 'Auby, Keeper of the Kingdoms, giving a happy thumbs up'
    : 'Auby, Keeper of the Kingdoms';
};

const showView = view => {
  nativeShell.dataset.view = view;
  gateView.hidden = view !== 'gate';
  registerView.hidden = view !== 'register';
  characterView.hidden = view !== 'character';
};

const escapeText = value => String(value ?? '')
  .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
  .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

const signedModifier = value => Number(value) >= 0 ? `+${Number(value)}` : String(Number(value));

const isOnline = () => navigator.onLine !== false;

function updateConnectionState() {
  const online = isOnline();
  connectionBanner.hidden = online;
  document.documentElement.dataset.connection = online ? 'online' : 'offline';
  return online;
}

function requireOnline(action = 'That live Guild action') {
  if (updateConnectionState()) return;
  throw new Error(`${action} needs a connection. Nothing has been changed.`);
}

function networkMessage(action) {
  return `${action} could not reach the Guild. Nothing has been changed; try again when the road is clear.`;
}

function liveActionError(error, action) {
  if (!isOnline() || (error instanceof TypeError && /fetch/i.test(error.message))) return networkMessage(action);
  return error instanceof Error ? error.message : `${action} could not be completed.`;
}


const DICE_HISTORY_LIMIT = 6;
let diceMode = 'normal';
let diceHistory = [];
let diceworks = null;

function secureD20() {
  if (!crypto || typeof crypto.getRandomValues !== 'function') {
    throw new Error('Secure dice are unavailable on this device.');
  }
  const range = 0x100000000;
  const limit = range - (range % 20);
  const values = new Uint32Array(1);
  let value = limit;
  while (value >= limit) {
    crypto.getRandomValues(values);
    value = values[0];
  }
  return (value % 20) + 1;
}

function secureDie(sides) {
  const size = Number(sides);
  if (!Number.isSafeInteger(size) || size < 2 || size > 100) throw new Error('That die is not supported by the Guild Diceworks.');
  if (!crypto || typeof crypto.getRandomValues !== 'function') throw new Error('Secure dice are unavailable on this device.');
  const range = 0x100000000;
  const limit = range - (range % size);
  const values = new Uint32Array(1);
  let value = limit;
  while (value >= limit) { crypto.getRandomValues(values); value = values[0]; }
  return (value % size) + 1;
}

function rollFormula(formula, modifier = 0) {
  const match = String(formula || '').trim().match(/^(\d+)d(4|6|8|10|12|20|100)$/i);
  if (!match) throw new Error('The Guild cannot read that damage formula.');
  const count = Number(match[1]);
  const sides = Number(match[2]);
  if (!Number.isSafeInteger(count) || count < 1 || count > 100) throw new Error('That many dice will not fit on the Guild desk.');
  const dice = Array.from({ length: count }, () => secureDie(sides));
  const diceTotal = dice.reduce((sum, die) => sum + die, 0);
  return { dice, total: diceTotal + Number(modifier || 0), formula: `${count}d${sides}` };
}

function rollD20Mode(mode) {
  const first = secureD20();
  if (mode === 'normal') return { dice: [first], natural: first };
  const second = secureD20();
  return {
    dice: [first, second],
    natural: mode === 'advantage' ? Math.max(first, second) : Math.min(first, second)
  };
}

function diceModeLabel(mode) {
  return mode === 'advantage' ? 'Advantage' : (mode === 'disadvantage' ? 'Disadvantage' : 'Normal');
}

function renderDiceHistory() {
  if (!diceworks) return;
  const list = diceworks.querySelector('[data-dice-history]');
  if (!list) return;
  list.innerHTML = diceHistory.map(entry => `<li><strong>${escapeText(entry.label)}</strong><span>${escapeText(entry.summary)}</span></li>`).join('');
  list.hidden = diceHistory.length === 0;
}

function celebrateNatural(natural) {
  if (!diceworks) return;
  const reaction = diceworks.querySelector('[data-dice-reaction]');
  const confetti = diceworks.querySelector('[data-dice-confetti]');
  reaction.textContent = '';
  reaction.dataset.reaction = 'none';
  confetti.replaceChildren();
  if (natural !== 20 && natural !== 1) return;
  const natural20 = natural === 20;
  reaction.dataset.reaction = natural20 ? 'natural-20' : 'natural-1';
  reaction.textContent = natural20 ? 'Natural 20!' : 'Natural 1 — Oh dear.';
  const pieces = natural20 ? 28 : 1;
  for (let index = 0; index < pieces; index += 1) {
    const piece = document.createElement('i');
    piece.setAttribute('aria-hidden', 'true');
    piece.style.setProperty('--dice-piece', String(index));
    if (!natural20) piece.classList.add('is-lonely');
    confetti.append(piece);
  }
}

function performManualRoll(sides, count, modifier) {
  if (!diceworks) return;
  try {
    const die = Number(sides);
    const diceCount = Number(count);
    const bonus = Number(modifier);
    if (![4, 6, 8, 10, 12, 20, 100].includes(die)) throw new Error('That die is not kept in the Guild drawer.');
    if (!Number.isSafeInteger(diceCount) || diceCount < 1 || diceCount > 20) throw new Error('Choose between 1 and 20 dice.');
    if (!Number.isSafeInteger(bonus) || bonus < -99 || bonus > 99) throw new Error('Choose a modifier between -99 and +99.');
    const rolled = rollFormula(`${diceCount}d${die}`, bonus);
    const diceText = rolled.dice.join(' + ');
    const summary = `${rolled.formula}: ${diceText} ${signedModifier(bonus)} = ${rolled.total}`;
    const natural = die === 20 && diceCount === 1 ? rolled.dice[0] : null;
    showDiceResult(`Manual ${rolled.formula}`, rolled.total, summary, 'manual', natural);
  } catch (error) {
    diceworks.querySelector('[data-dice-live]').textContent = error instanceof Error ? error.message : 'The manual dice could not be rolled.';
  }
}

function performTrainingRoll(label, modifier, kind = 'check') {
  if (!diceworks) return;
  try {
    const rolled = rollD20Mode(diceMode);
    const total = rolled.natural + Number(modifier || 0);
    const diceText = rolled.dice.join(' / ');
    const summary = `${diceText} ${signedModifier(modifier)} = ${total} · ${diceModeLabel(diceMode)}`;
    const resultLabel = diceworks.querySelector('[data-dice-label]');
    const resultTotal = diceworks.querySelector('[data-dice-total]');
    const resultMath = diceworks.querySelector('[data-dice-math]');
    resultLabel.textContent = label;
    resultTotal.textContent = String(total);
    resultMath.textContent = summary;
    diceworks.querySelector('[data-dice-peek]').textContent = `${label}: ${total}`;
    diceworks.dataset.kind = kind;
    diceworks.classList.add('has-result');
    celebrateNatural(rolled.natural);
    diceHistory.unshift({ label, summary });
    diceHistory.splice(DICE_HISTORY_LIMIT);
    renderDiceHistory();
    const live = diceworks.querySelector('[data-dice-live]');
    live.textContent = `${label}: ${summary}${rolled.natural === 20 ? '. Natural 20.' : (rolled.natural === 1 ? '. Natural 1. Auby says: The Guild has elected not to record that one.' : '.')}`;
  } catch (error) {
    diceworks.querySelector('[data-dice-live]').textContent = error instanceof Error ? error.message : 'The dice could not be rolled.';
  }
}

function showDiceResult(label, total, summary, kind = 'roll', natural = null) {
  if (!diceworks) return;
  const resultLabel = diceworks.querySelector('[data-dice-label]');
  const resultTotal = diceworks.querySelector('[data-dice-total]');
  const resultMath = diceworks.querySelector('[data-dice-math]');
  resultLabel.textContent = label;
  resultTotal.textContent = String(total);
  resultMath.textContent = summary;
  diceworks.querySelector('[data-dice-peek]').textContent = `${label}: ${total}`;
  diceworks.dataset.kind = kind;
  diceworks.classList.add('has-result');
  celebrateNatural(natural);
  diceHistory.unshift({ label, summary });
  diceHistory.splice(DICE_HISTORY_LIMIT);
  renderDiceHistory();
  diceworks.querySelector('[data-dice-live]').textContent = `${label}: ${summary}${natural === 20 ? '. Natural 20. Critical hit.' : (natural === 1 ? '. Natural 1. Auby says: The Guild has elected not to record that one.' : '.')}`;
}

function performAttackRoll(attack) {
  if (!diceworks) return;
  try {
    const rolled = rollD20Mode(diceMode);
    const modifier = Number(attack?.attack_bonus || 0);
    const total = rolled.natural + modifier;
    showDiceResult(`${attack.label} — Attack`, total, `${rolled.dice.join(' / ')} ${signedModifier(modifier)} = ${total} · ${diceModeLabel(diceMode)} · to hit`, 'attack', rolled.natural);
  } catch (error) {
    diceworks.querySelector('[data-dice-live]').textContent = error instanceof Error ? error.message : 'The attack roll could not be made.';
  }
}

function performDamageRoll(attack, critical = false) {
  if (!diceworks) return;
  try {
    const formula = critical ? attack?.critical_damage_die : attack?.damage_die;
    const modifier = Number(attack?.damage_modifier || 0);
    const rolled = rollFormula(formula, modifier);
    const label = `${attack.label} — ${critical ? 'Critical Damage' : 'Damage'}`;
    const diceText = rolled.dice.join(' + ');
    const summary = `${rolled.formula}: ${diceText} ${signedModifier(modifier)} = ${rolled.total} ${attack?.damage_type || 'damage'}`;
    showDiceResult(label, rolled.total, summary, critical ? 'critical-damage' : 'damage');
  } catch (error) {
    diceworks.querySelector('[data-dice-live]').textContent = error instanceof Error ? error.message : 'The damage roll could not be made.';
  }
}

function combatPanel(character) {
  const panel = document.createElement('section');
  panel.className = 'native-dashboard-panel native-combat';
  panel.dataset.nativePanel = 'combat';
  panel.hidden = true;
  const attacks = Array.isArray(character.attacks) ? character.attacks : [];
  const cards = attacks.map((attack, index) => {
    const properties = Array.isArray(attack.properties) && attack.properties.length ? `<ul class="combat-properties">${attack.properties.map(property => `<li>${escapeText(String(property).replace(/^./, letter => letter.toUpperCase()))}</li>`).join('')}</ul>` : '';
    return `<article class="native-attack-card" data-attack-index="${index}"><header><div><p class="eyebrow">${escapeText(attack.range || 'Weapon attack')}</p><h3>${escapeText(attack.label)}</h3></div><strong>${escapeText(signedModifier(attack.attack_bonus))}</strong></header><p>${escapeText(attack.description || '')}</p><dl><div><dt>Attack ability</dt><dd>${escapeText(attack.ability || '—')}</dd></div><div><dt>Damage</dt><dd>${escapeText(`${attack.damage_die || '—'} ${signedModifier(attack.damage_modifier)} ${attack.damage_type || 'damage'}`)}</dd></div></dl>${properties}<div class="combat-rolls"><button type="button" data-combat-roll="attack">⚔ Roll Attack</button><button type="button" data-combat-roll="damage">✹ Roll Damage</button><button type="button" class="critical-roll" data-combat-roll="critical">★ Critical Damage</button></div></article>`;
  }).join('');
  panel.innerHTML = `<section class="native-panel"><div class="panel-heading"><p class="eyebrow">The Clash of the Ledger</p><h2>Attacks &amp; Weapons</h2><p>Equipped weapons come directly from the authoritative Companion ledger. Attack, damage and critical damage use the shared Guild Diceworks.</p></div>${cards || '<div class="native-empty"><strong>No weapon is readied.</strong><p>Equip a weapon in the Companion and refresh the Pocket.</p></div>'}</section>`;
  panel.querySelectorAll('[data-attack-index]').forEach(card => {
    const attack = attacks[Number(card.dataset.attackIndex)];
    card.querySelectorAll('[data-combat-roll]').forEach(button => button.addEventListener('click', () => {
      if (button.dataset.combatRoll === 'attack') performAttackRoll(attack);
      else performDamageRoll(attack, button.dataset.combatRoll === 'critical');
    }));
  });
  return panel;
}

async function persistSpellSlot(character, slot, action) {
  requireOnline('Changing a spell reserve');
  if (!accessToken) throw new Error('The Guild key is not available.');
  const response = await fetch(SPELL_SLOTS(character.id), {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${accessToken}`,
      'Content-Type': 'application/json',
      Accept: 'application/json'
    },
    cache: 'no-store',
    body: JSON.stringify({ level: slot.level, action, expected_remaining: slot.remaining })
  });
  const payload = await response.json().catch(() => ({}));
  if (response.status === 401 || response.status === 403) {
    await forgetStoredToken();
    showSignedOut('Your Guild key needs renewing.');
    throw new Error('Your Guild key needs renewing.');
  }
  if (!response.ok) throw new Error(payload.message || 'The spell-slot ledger could not be updated.');
  character.spellcasting.slots = Array.isArray(payload.slots) ? payload.slots : [];
  return character.spellcasting.slots;
}

function spellbookPanel(character) {
  const panel = document.createElement('section');
  panel.className = 'native-dashboard-panel native-spellbook';
  panel.dataset.nativePanel = 'spells';
  panel.hidden = true;
  const casting = character.spellcasting || {};
  const spells = Array.isArray(character.spellbook) ? [...character.spellbook] : [];
  spells.sort((left, right) => (Number(left.level ?? (left.group === 'cantrips' ? 0 : 99)) - Number(right.level ?? (right.group === 'cantrips' ? 0 : 99))) || String(left.name || '').localeCompare(String(right.name || '')));

  const measures = casting.ability ? `<div class="spellcasting-measures"><div><span>Spellcasting</span><strong>${escapeText(casting.ability)}</strong></div>${casting.attack_bonus !== null && casting.attack_bonus !== undefined ? `<div><span>Spell Attack</span><strong>${escapeText(signedModifier(casting.attack_bonus))}</strong></div>` : ''}${casting.save_dc !== null && casting.save_dc !== undefined ? `<div><span>Save DC</span><strong>${escapeText(casting.save_dc)}</strong></div>` : ''}</div>` : '';
  panel.innerHTML = `<section class="native-panel"><div class="panel-heading"><p class="eyebrow">The Pocket Spellbook</p><h2>Spellkeeper's Register</h2><p>Your known magic and reserves come directly from the authoritative Companion ledger.</p></div>${measures}<div data-spell-reserves></div><div data-spell-register></div></section>`;

  if (Number.isFinite(Number(casting.attack_bonus))) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'spell-attack-roll';
    button.textContent = '✦ Roll Spell Attack';
    button.addEventListener('click', () => performTrainingRoll('Spell Attack', Number(casting.attack_bonus), 'spell-attack'));
    panel.querySelector('.spellcasting-measures')?.after(button);
  }

  const reserveHost = panel.querySelector('[data-spell-reserves]');
  const slots = Array.isArray(casting.slots) ? casting.slots : [];
  if (slots.length) {
    const section = document.createElement('section');
    section.className = 'spell-reserves';
    section.innerHTML = '<h3>Spell Reserves</h3><p>Use and restore slots against the shared live ledger.</p>';
    const list = document.createElement('div');
    list.className = 'spell-slot-list';
    for (const slot of slots) {
      const row = document.createElement('article');
      row.className = 'spell-slot-row';
      const draw = () => {
        row.innerHTML = `<div><span>Level ${escapeText(slot.level)}</span><strong>${escapeText(slot.remaining)} / ${escapeText(slot.total)}</strong><small>${escapeText(slot.expended)} expended</small></div><div class="spell-slot-actions"><button type="button" data-slot-action="spend" ${Number(slot.remaining) <= 0 ? 'disabled' : ''}>Use Slot</button><button type="button" data-slot-action="recover" ${Number(slot.expended) <= 0 ? 'disabled' : ''}>Restore Slot</button></div><small class="spell-slot-message" role="status" aria-live="polite"></small>`;
        row.querySelectorAll('[data-slot-action]').forEach(button => button.addEventListener('click', async () => {
          const message = row.querySelector('.spell-slot-message');
          row.querySelectorAll('button').forEach(control => { control.disabled = true; });
          message.textContent = 'Updating the live ledger…';
          try {
            const updated = await persistSpellSlot(character, slot, button.dataset.slotAction);
            const next = updated.find(candidate => Number(candidate.level) === Number(slot.level));
            if (next) Object.assign(slot, next);
            draw();
          } catch (error) {
            draw();
            row.querySelector('.spell-slot-message').textContent = liveActionError(error, 'The spell-slot ledger');
          }
        }));
      };
      draw();
      list.append(row);
    }
    section.append(list);
    reserveHost.append(section);
  } else if (casting.ability) {
    reserveHost.innerHTML = '<div class="native-empty"><strong>No standard spell-slot reserve is recorded.</strong><p>Special casting resources remain governed by their own Companion ledger.</p></div>';
  }

  const register = panel.querySelector('[data-spell-register]');
  if (!spells.length) {
    register.innerHTML = '<div class="native-empty"><strong>No spells are recorded.</strong><p>The Pocket will reflect the Companion spellbook after Refresh.</p></div>';
    return panel;
  }
  const grouped = new Map();
  for (const spell of spells) {
    const level = Number.isInteger(spell.level) ? Number(spell.level) : (spell.group === 'cantrips' ? 0 : null);
    const key = level === 0 ? 'Cantrips' : (level === null ? 'Other Spells' : `Level ${level}`);
    if (!grouped.has(key)) grouped.set(key, []);
    grouped.get(key).push(spell);
  }
  for (const [heading, entries] of grouped) {
    const section = document.createElement('section');
    section.className = 'spell-level-group';
    section.innerHTML = `<h3>${escapeText(heading)}</h3>`;
    for (const spell of entries) {
      const entry = document.createElement('details');
      entry.className = 'spell-entry';
      const unresolved = spell.resolved === false ? '<span class="spell-unresolved">Reference only</span>' : '';
      entry.innerHTML = `<summary><span><strong>${escapeText(spell.name || 'Unknown Spell')}</strong><small>${escapeText(spell.school || (spell.group === 'cantrips' ? 'Cantrip' : 'Spell'))}</small></span>${unresolved}</summary><div class="spell-detail"><dl><div><dt>Casting time</dt><dd>${escapeText(spell.casting_time || '—')}</dd></div><div><dt>Range</dt><dd>${escapeText(spell.range || '—')}</dd></div><div><dt>Components</dt><dd>${escapeText(spell.components || '—')}</dd></div><div><dt>Duration</dt><dd>${escapeText(spell.duration || '—')}</dd></div></dl>${spell.rules_text ? `<p>${escapeText(spell.rules_text)}</p>` : ''}${spell.higher_levels ? `<p><strong>At higher levels.</strong> ${escapeText(spell.higher_levels)}</p>` : ''}</div>`;
      section.append(entry);
    }
    register.append(section);
  }
  return panel;
}

function equipmentPanel(character) {
  const panel = document.createElement('section');
  panel.className = 'native-equipment';
  const equipment = Array.isArray(character.equipment) ? character.equipment : [];
  const rows = equipment.map(item => `<li class="equipment-row"><div><strong>${escapeText(item.label)}</strong><small>${escapeText(item.category || 'Equipment')}${item.equipped ? ' · Equipped' : ''}</small></div><span>×${escapeText(item.quantity ?? 1)}</span>${Number(item.total_weight || 0) > 0 ? `<small>${escapeText(item.total_weight)} lb</small>` : '<small>—</small>'}</li>`).join('');
  panel.innerHTML = `<section class="native-panel"><div class="panel-heading"><p class="eyebrow">Adventurer's Pack</p><h2>Equipment</h2><p>Your pack is read-only in the native Pocket. Equipment changes remain certified in the Companion.</p></div>${rows ? `<ul class="equipment-list">${rows}</ul>` : '<div class="native-empty"><strong>The pack is empty.</strong><p>Add equipment in the Companion and refresh the Pocket.</p></div>'}</section>`;
  return panel;
}

function createDiceworks() {
  const tray = document.createElement('aside');
  tray.className = 'native-diceworks';
  tray.innerHTML = `<button type="button" class="diceworks-toggle" aria-expanded="false"><span><small>Guild Diceworks</small><strong data-dice-peek>Ready to roll</strong></span><span aria-hidden="true">🎲</span></button>
    <div class="diceworks-body" hidden>
      <div class="dice-mode" role="group" aria-label="Roll mode">
        <button type="button" data-dice-mode="normal" aria-pressed="true">Normal</button>
        <button type="button" data-dice-mode="advantage" aria-pressed="false">Advantage</button>
        <button type="button" data-dice-mode="disadvantage" aria-pressed="false">Disadvantage</button>
      </div>
      <section class="dice-drawer" aria-label="Manual dice drawer">
        <div class="dice-drawer-heading"><strong>Dice Drawer</strong><small>Manual rolls share this session's history.</small></div>
        <div class="dice-types" role="group" aria-label="Choose a die">
          ${[4,6,8,10,12,20,100].map((sides, index) => `<button type="button" data-manual-die="${sides}" aria-pressed="${index === 0 ? 'true' : 'false'}">d${sides}</button>`).join('')}
        </div>
        <div class="dice-manual-controls">
          <label>Dice<input type="number" inputmode="numeric" min="1" max="20" step="1" value="1" data-manual-count></label>
          <label>Modifier<input type="number" inputmode="numeric" min="-99" max="99" step="1" value="0" data-manual-modifier></label>
          <button type="button" data-manual-roll>Roll</button>
        </div>
      </section>
      <div class="dice-result" aria-live="off"><span data-dice-label>Choose a roll</span><strong data-dice-total>—</strong><small data-dice-math>Use an adventurer action or open the Dice Drawer.</small></div>
      <div class="dice-reaction" data-dice-reaction="none" data-dice-reaction></div><div class="dice-confetti" data-dice-confetti></div>
      <ol class="dice-history" data-dice-history hidden></ol>
      <p class="dice-live sr-only" data-dice-live aria-live="polite"></p>
    </div>`;
  const toggle = tray.querySelector('.diceworks-toggle');
  const body = tray.querySelector('.diceworks-body');
  toggle.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!open));
    body.hidden = open;
  });
  tray.querySelectorAll('[data-dice-mode]').forEach(button => button.addEventListener('click', () => {
    diceMode = button.dataset.diceMode;
    tray.querySelectorAll('[data-dice-mode]').forEach(option => option.setAttribute('aria-pressed', String(option === button)));
  }));
  let manualDie = 4;
  tray.querySelectorAll('[data-manual-die]').forEach(button => button.addEventListener('click', () => {
    manualDie = Number(button.dataset.manualDie);
    tray.querySelectorAll('[data-manual-die]').forEach(option => option.setAttribute('aria-pressed', String(option === button)));
  }));
  tray.querySelector('[data-manual-roll]').addEventListener('click', () => {
    const count = tray.querySelector('[data-manual-count]');
    const modifier = tray.querySelector('[data-manual-modifier]');
    performManualRoll(manualDie, Number(count.value), Number(modifier.value));
  });
  return tray;
}

const showSignedOut = (message = 'Not connected to the Guild.') => {
  accessToken = null;
  pending = null;
  setAuby('gate');
  enter.hidden = false;
  openRegister.hidden = true;
  leave.hidden = true;
  liveCharacters = [];
  showView('gate');
  setStatus(message);
};

const showSignedIn = session => {
  // A restored secure session can complete before any navigation occurs. Keep the
  // cold-launch shell explicitly in Gate layout so it receives the same edge-to-edge
  // viewport treatment as a Gate reached via Back navigation.
  showView('gate');
  setAuby('success');
  enter.hidden = true;
  openRegister.hidden = false;
  leave.hidden = false;
  setStatus(
    `The Keeper remembers your key. Welcome, ${session.user?.display_name || 'adventurer'}!`,
    'success'
  );
};

async function fetchCharacters() {
  requireOnline('Refreshing the Adventurers’ Register');
  if (!accessToken) throw new Error('The Guild key is not available.');
  const response = await fetch(CHARACTERS, {
    headers: { Authorization: `Bearer ${accessToken}` },
    cache: 'no-store'
  });
  if (response.status === 401 || response.status === 403) {
    await forgetStoredToken();
    showSignedOut('Your Guild key needs renewing.');
    throw new Error('Your Guild key needs renewing.');
  }
  if (!response.ok) throw new Error('The Adventurers\' Register could not be opened.');
  const payload = await response.json();
  return Array.isArray(payload.characters) ? payload.characters : [];
}

function portraitMarkup(character) {
  const portrait = character?.portrait;
  if (portrait?.url && (portrait.kind === 'image' || portrait.kind === 'svg')) {
    return `<img src="${escapeText(portrait.url)}" alt="Portrait of ${escapeText(character.name)}" loading="lazy" decoding="async" data-portrait-image />`;
  }
  return '<span class="portrait-fallback" aria-hidden="true">✦</span>';
}

function renderRegister(characters) {
  liveCharacters = characters;
  characterList.replaceChildren();
  if (!characters.length) {
    characterList.innerHTML = '<div class="empty-register"><strong>No adventurers are registered yet.</strong><span>Create a character in the Companion and Auby will find them here.</span></div>';
    return;
  }
  for (const character of characters) {
    const card = document.createElement('button');
    card.type = 'button';
    card.className = 'character-card';
    card.dataset.characterId = character.id;
    card.innerHTML = `<span class="character-portrait">${portraitMarkup(character)}</span>
      <span class="character-identity"><strong>${escapeText(character.name)}</strong>
      <span>Level ${escapeText(character.level)} ${escapeText(character.race)} ${escapeText(character.class)}</span></span>
      <span class="character-hp"><strong>${escapeText(character.hp?.current)}/${escapeText(character.hp?.maximum)}</strong><span>HP</span></span>
      <span class="chevron" aria-hidden="true">›</span>`;
    card.querySelector('[data-portrait-image]')?.addEventListener('error', event => {
      event.currentTarget.replaceWith(Object.assign(document.createElement('span'), { className: 'portrait-fallback', textContent: '✦' }));
    }, { once: true });
    card.addEventListener('click', () => openCharacter(character.id));
    characterList.append(card);
  }
}

async function openAdventurersRegister() {
  if (registerLoading) return;
  registerLoading = true;
  showView('register');
  registerStatus.hidden = false;
  registerStatus.textContent = 'Auby is opening the Adventurers\' Register…';
  registerRefresh.disabled = true;
  registerRefresh.setAttribute('aria-busy', 'true');
  if (!liveCharacters.length) characterList.replaceChildren();
  try {
    const characters = await fetchCharacters();
    renderRegister(characters);
    registerStatus.textContent = `${characters.length} adventurer${characters.length === 1 ? '' : 's'} registered.`;
    return true;
  } catch (error) {
    if (!accessToken) return;
    registerStatus.textContent = liveActionError(error, 'The Adventurers\' Register');
    return false;
  } finally {
    registerLoading = false;
    registerRefresh.disabled = false;
    registerRefresh.removeAttribute('aria-busy');
  }
}

async function persistVitality(character, body) {
  requireOnline('Updating Adventuring Measures');
  const hp = character.hp || {};
  const response = await fetch(`${CHARACTERS}/${encodeURIComponent(character.id)}/vitality`, {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${accessToken}`,
      'Content-Type': 'application/json',
      Accept: 'application/json'
    },
    cache: 'no-store',
    body: JSON.stringify({
      ...body,
      expected_current: hp.current,
      expected_temporary: hp.temporary
    })
  });
  const payload = await response.json().catch(() => ({}));
  if (response.status === 401 || response.status === 403) {
    await forgetStoredToken();
    showSignedOut('Your Guild key needs renewing.');
    throw new Error('Your Guild key needs renewing.');
  }
  if (!response.ok) throw new Error(payload.message || 'Adventuring Measures could not be updated.');
  character.hp = payload.hp;
  return payload.hp;
}

function vitalityPanel(character) {
  const panel = document.createElement('section');
  panel.className = 'native-panel vitality-panel';
  panel.innerHTML = `<div class="panel-heading"><p class="eyebrow">Adventurer's Vitality</p><h2>Adventuring Measures</h2><p>Current and temporary HP are live. Maximum HP remains Guild-certified.</p></div>
    <form id="native-vitality-form">
      <div class="vitality-fields">
        <label>Current HP<input id="native-current-hp" type="number" inputmode="numeric" min="0" max="${escapeText(character.hp?.maximum)}" step="1" required value="${escapeText(character.hp?.current)}"></label>
        <label>Temporary HP<input id="native-temp-hp" type="number" inputmode="numeric" min="0" max="999" step="1" required value="${escapeText(character.hp?.temporary)}"></label>
        <div class="certified-measure"><span>Maximum HP</span><strong>${escapeText(character.hp?.maximum)}</strong><small>Read-only</small></div>
      </div>
      <button class="native-action" type="submit">Save HP</button>
      <div class="vitality-adjust"><label>Damage or healing<input id="native-vitality-amount" type="number" inputmode="numeric" min="1" max="9999" step="1" value="5" required></label><div><button type="button" data-vitality-action="damage">− Damage</button><button type="button" data-vitality-action="heal">+ Healing</button></div></div>
      <p id="vitality-message" class="vitality-message" role="status" aria-live="polite"></p>
    </form>`;
  const form = panel.querySelector('#native-vitality-form');
  const current = panel.querySelector('#native-current-hp');
  const temporary = panel.querySelector('#native-temp-hp');
  const amount = panel.querySelector('#native-vitality-amount');
  const message = panel.querySelector('#vitality-message');
  const controls = [...panel.querySelectorAll('input, button')];
  let busy = false;
  const setBusy = value => { busy = value; controls.forEach(control => { control.disabled = value; }); };
  const sync = hp => { current.value = hp.current; temporary.value = hp.temporary; };
  const perform = async body => {
    if (busy) return;
    setBusy(true); message.textContent = 'Updating the live ledger…';
    try {
      const hp = await persistVitality(character, body);
      sync(hp); message.textContent = 'Adventuring Measures updated.';
    } catch (error) {
      message.textContent = liveActionError(error, 'Adventuring Measures');
    } finally { setBusy(false); }
  };
  form.addEventListener('submit', event => {
    event.preventDefault();
    const nextCurrent = Number(current.value), nextTemporary = Number(temporary.value);
    if (!current.validity.valid || !temporary.validity.valid || !Number.isSafeInteger(nextCurrent) || !Number.isSafeInteger(nextTemporary)) {
      message.textContent = 'Enter valid whole-number HP values.'; return;
    }
    perform({ current: nextCurrent, temporary: nextTemporary });
  });
  panel.querySelectorAll('[data-vitality-action]').forEach(button => button.addEventListener('click', () => {
    const n = Number(amount.value);
    if (!amount.validity.valid || !Number.isSafeInteger(n)) { message.textContent = 'Enter a damage or healing amount between 1 and 9999.'; return; }
    perform({ action: button.dataset.vitalityAction, amount: n });
  }));
  return panel;
}

function trainingPanel(character) {
  const panel = document.createElement('section');
  panel.className = 'native-panel native-training';
  const saves = Object.entries(character.saving_throws || {});
  const skills = Object.entries(character.skills || {});
  const saveRows = saves.map(([ability, save]) => {
    const trained = save?.proficient ? '<span class="training-badge">Proficient</span>' : '<span class="training-badge training-badge--quiet">Untrained</span>';
    return `<li><button type="button" class="training-row${save?.proficient ? ' is-proficient' : ''}" data-training-roll data-roll-kind="save" data-roll-label="${escapeText(ability)} Saving Throw" data-roll-modifier="${escapeText(save?.modifier ?? 0)}"><span class="training-mark" aria-hidden="true">${save?.proficient ? '●' : '○'}</span><span class="training-name"><strong>${escapeText(ability)}</strong><small>Saving throw</small></span><span class="training-state">${trained}</span><strong class="training-modifier">${escapeText(signedModifier(save?.modifier ?? 0))}</strong></button></li>`;
  }).join('');
  const skillRows = skills.map(([identifier, skill]) => {
    const label = skill?.label || String(identifier).replaceAll('-', ' ').replace(/\b\w/g, letter => letter.toUpperCase());
    const mastery = skill?.expertise ? 'Expertise' : (skill?.proficient ? 'Proficient' : 'Untrained');
    const badgeClass = skill?.expertise ? ' training-badge--expertise' : (skill?.proficient ? '' : ' training-badge--quiet');
    return `<li><button type="button" class="training-row${skill?.proficient ? ' is-proficient' : ''}${skill?.expertise ? ' has-expertise' : ''}" data-training-roll data-roll-kind="skill" data-roll-label="${escapeText(label)}" data-roll-modifier="${escapeText(skill?.modifier ?? 0)}"><span class="training-mark" aria-hidden="true">${skill?.expertise ? '◆' : (skill?.proficient ? '●' : '○')}</span><span class="training-name"><strong>${escapeText(label)}</strong><small>${escapeText(skill?.ability || '—')}</small></span><span class="training-state"><span class="training-badge${badgeClass}">${mastery}</span></span><strong class="training-modifier">${escapeText(signedModifier(skill?.modifier ?? 0))}</strong></button></li>`;
  }).join('');
  panel.innerHTML = `<div class="panel-heading"><p class="eyebrow">Adventurer's Training</p><h2>Skills &amp; Saving Throws</h2><p>These modifiers and proficiencies come from the authoritative Companion ledger.</p></div>
    <section class="training-section" aria-labelledby="native-saving-throws-title"><h3 id="native-saving-throws-title">Saving Throws</h3><ul class="training-list">${saveRows}</ul></section>
    <section class="training-section" aria-labelledby="native-skills-title"><h3 id="native-skills-title">Skills</h3><ul class="training-list">${skillRows}</ul></section>`;
  panel.querySelectorAll('[data-training-roll]').forEach(button => button.addEventListener('click', () => {
    performTrainingRoll(button.dataset.rollLabel, Number(button.dataset.rollModifier), button.dataset.rollKind);
  }));
  return panel;
}

async function openExternalPage(url) {
  try {
    await Browser.open({ url });
  } catch {
    window.location.href = url;
  }
}

function privacySupportPanel() {
  const panel = document.createElement('section');
  panel.className = 'native-panel native-privacy-support';
  panel.innerHTML = `<div class="panel-heading"><p class="eyebrow">The Registrar's Desk</p><h2>Privacy &amp; Support</h2><p>The Guild keeps its public privacy charter and support desk on the Great MarketRealm website.</p></div>
    <div class="privacy-support-actions">
      <button type="button" data-external-url="${PRIVACY_URL}">Privacy Policy</button>
      <button type="button" class="secondary" data-external-url="${SUPPORT_URL}">Support</button>
      <button type="button" class="secondary" data-external-url="${DELETE_ACCOUNT_URL}">Delete Account &amp; Data</button>
    </div>`;
  panel.querySelectorAll('[data-external-url]').forEach(button => button.addEventListener('click', () => openExternalPage(button.dataset.externalUrl)));
  return panel;
}

function placeholderPanel(title, copy) {
  const panel = document.createElement('section');
  panel.className = 'native-panel native-coming-soon';
  panel.innerHTML = `<p class="eyebrow">Native Pocket</p><h2>${escapeText(title)}</h2><p>${escapeText(copy)}</p>`;
  return panel;
}

function openCharacter(id, initialTab = 'overview') {
  const character = liveCharacters.find(candidate => candidate.id === id);
  if (!character) return;
  selectedCharacterId = id;
  characterTitle.textContent = character.name;
  characterLedger.replaceChildren();

  const hero = document.createElement('section');
  hero.className = 'native-character-hero';
  hero.innerHTML = `<div class="hero-portrait">${portraitMarkup(character)}</div>
    <div><p class="eyebrow">Adventurer Overview</p><h2>${escapeText(character.name)}</h2><p>Level ${escapeText(character.level)} ${escapeText(character.race)} ${escapeText(character.class)}</p></div>`;

  const overview = document.createElement('section');
  overview.className = 'native-dashboard-panel';
  overview.dataset.nativePanel = 'overview';
  overview.hidden = initialTab !== 'overview';
  overview.append(vitalityPanel(character));
  const measures = document.createElement('div');
  measures.className = 'measure-grid';
  measures.innerHTML = `<div><span>Armour Class</span><strong>${escapeText(character.armour_class)}</strong></div><button type="button" class="initiative-roll" data-initiative-roll aria-label="Roll initiative with modifier ${escapeText(signedModifier(character.initiative_modifier ?? 0))}"><span>Initiative</span><strong>${escapeText(character.initiative)}</strong><small>Roll d20 ${escapeText(signedModifier(character.initiative_modifier ?? 0))}</small></button><div><span>Speed</span><strong>${escapeText(character.speed_feet)} ft</strong></div><div><span>Proficiency</span><strong>${signedModifier(character.proficiency_bonus)}</strong></div>`;
  measures.querySelector('[data-initiative-roll]')?.addEventListener('click', () => performTrainingRoll('Initiative', Number(character.initiative_modifier ?? 0), 'initiative'));
  overview.append(measures);

  const characterPanel = document.createElement('section');
  characterPanel.className = 'native-dashboard-panel'; characterPanel.dataset.nativePanel = 'character'; characterPanel.hidden = initialTab !== 'character';
  characterPanel.innerHTML = `<div class="ability-grid">${Object.entries(character.abilities || {}).map(([ability, score]) => { const modifier = character.ability_modifiers?.[ability] ?? 0; return `<button type="button" data-ability-roll data-roll-label="${escapeText(ability)} Ability Check" data-roll-modifier="${escapeText(modifier)}"><span>${escapeText(ability)}</span><strong>${escapeText(score)}</strong><small>${escapeText(signedModifier(modifier))}</small></button>`; }).join('')}</div>`;
  characterPanel.querySelectorAll('[data-ability-roll]').forEach(button => button.addEventListener('click', () => performTrainingRoll(button.dataset.rollLabel, Number(button.dataset.rollModifier), 'ability')));
  characterPanel.append(trainingPanel(character));

  const combat = combatPanel(character);
  combat.hidden = initialTab !== 'combat';
  const spells = spellbookPanel(character);
  spells.hidden = initialTab !== 'spells';
  const more = document.createElement('section');
  more.className = 'native-dashboard-panel';
  more.dataset.nativePanel = 'more';
  more.hidden = initialTab !== 'more';
  more.append(equipmentPanel(character), privacySupportPanel());

  const dock = document.createElement('nav');
  dock.className = 'native-dashboard-dock'; dock.setAttribute('aria-label', 'Pocket character navigation');
  for (const [key, label] of [['overview','Overview'],['character','Character'],['combat','Combat'],['spells','Spellbook'],['more','More']]) {
    const button = document.createElement('button'); button.type = 'button'; button.textContent = label; button.dataset.nativeTab = key; button.setAttribute('aria-current', key === initialTab ? 'page' : 'false');
    button.addEventListener('click', () => {
      characterLedger.querySelectorAll('[data-native-panel]').forEach(panel => { panel.hidden = panel.dataset.nativePanel !== key; });
      dock.querySelectorAll('[data-native-tab]').forEach(tab => tab.setAttribute('aria-current', tab.dataset.nativeTab === key ? 'page' : 'false'));
    });
    dock.append(button);
  }
  diceHistory = [];
  diceMode = 'normal';
  diceworks = createDiceworks();
  characterLedger.append(hero, overview, characterPanel, combat, spells, more, diceworks, dock);
  showView('character');
}

function activeCharacterTab() {
  return characterLedger.querySelector('[data-native-tab][aria-current="page"]')?.dataset.nativeTab || 'overview';
}

async function refreshOpenCharacter() {
  if (characterRefreshLoading || !selectedCharacterId) return;
  characterRefreshLoading = true;
  const id = selectedCharacterId;
  const tab = activeCharacterTab();
  const rememberedHistory = [...diceHistory];
  const rememberedMode = diceMode;
  try {
    const characters = await fetchCharacters();
    renderRegister(characters);
    const refreshed = characters.find(character => character.id === id);
    if (!refreshed) {
      showView('register');
      registerStatus.hidden = false;
      registerStatus.textContent = 'That adventurer is no longer in this Register.';
      return false;
    }
    openCharacter(id, tab);
    diceHistory = rememberedHistory;
    diceMode = rememberedMode;
    if (diceworks) {
      diceworks.querySelectorAll('[data-dice-mode]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.diceMode === diceMode)));
      renderDiceHistory();
    }
    return true;
  } catch (error) {
    if (!accessToken) return;
    showPullMessage(characterView, liveActionError(error, 'Refreshing this adventurer'));
    return false;
  } finally {
    characterRefreshLoading = false;
  }
}

function pullIndicator(view) {
  let indicator = view.querySelector('[data-pull-refresh]');
  if (indicator) return indicator;
  indicator = document.createElement('div');
  indicator.className = 'pull-refresh';
  indicator.dataset.pullRefresh = '';
  indicator.setAttribute('role', 'status');
  indicator.setAttribute('aria-live', 'polite');
  indicator.textContent = '↓ Pull to refresh';
  view.prepend(indicator);
  return indicator;
}

function showPullMessage(view, message, timeout = 1800) {
  const indicator = pullIndicator(view);
  indicator.textContent = message;
  indicator.classList.add('is-visible');
  window.setTimeout(() => indicator.classList.remove('is-visible'), timeout);
}

function installPullToRefresh(view, refresh) {
  const indicator = pullIndicator(view);
  let startY = null;
  let distance = 0;
  const threshold = 72;
  view.addEventListener('touchstart', event => {
    if (window.scrollY > 0 || event.touches.length !== 1) { startY = null; return; }
    startY = event.touches[0].clientY;
    distance = 0;
  }, { passive: true });
  view.addEventListener('touchmove', event => {
    if (startY === null || window.scrollY > 0 || event.touches.length !== 1) return;
    distance = Math.max(0, Math.min(110, event.touches[0].clientY - startY));
    if (distance <= 0) return;
    event.preventDefault();
    indicator.classList.add('is-visible');
    indicator.style.setProperty('--pull-distance', `${distance}px`);
    indicator.textContent = distance >= threshold ? '↻ Release to refresh' : '↓ Pull to refresh';
  }, { passive: false });
  view.addEventListener('touchend', async () => {
    if (startY === null) return;
    const shouldRefresh = distance >= threshold;
    startY = null;
    indicator.style.removeProperty('--pull-distance');
    if (!shouldRefresh) { indicator.classList.remove('is-visible'); return; }
    if (!isOnline()) {
      indicator.textContent = 'Offline — the Ledger has not changed.';
      window.setTimeout(() => indicator.classList.remove('is-visible'), 1800);
      return;
    }
    indicator.textContent = 'Auby is checking the Ledger…';
    indicator.classList.add('is-refreshing');
    try {
      const refreshed = await refresh();
      if (refreshed !== false && accessToken && isOnline()) indicator.textContent = '✓ Ledger refreshed';
    } finally {
      indicator.classList.remove('is-refreshing');
      window.setTimeout(() => indicator.classList.remove('is-visible'), 1200);
    }
  }, { passive: true });
  view.addEventListener('touchcancel', () => {
    startY = null; distance = 0;
    indicator.style.removeProperty('--pull-distance');
    indicator.classList.remove('is-visible');
  }, { passive: true });
}

const base64url = bytes => btoa(String.fromCharCode(...bytes))
  .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
const randomValue = length => base64url(crypto.getRandomValues(new Uint8Array(length)));
const sha256 = async value => new Uint8Array(
  await crypto.subtle.digest('SHA-256', new TextEncoder().encode(value))
);

async function prepareSecureStorage() {
  // Never permit the plugin's web fallback for bearer tokens.
  if (!Capacitor.isNativePlatform()) {
    throw new Error('Pocket secure storage is available only inside the native app.');
  }
  await SecureStorage.setKeyPrefix(STORAGE_PREFIX);
}

async function forgetStoredToken() {
  try {
    await prepareSecureStorage();
    await SecureStorage.remove(TOKEN_KEY);
  } catch {
    // Sign-out must still clear the in-memory credential if secure storage is unavailable.
  }
}

async function verifySession(token) {
  requireOnline('Checking the Guild key');
  const response = await fetch(SESSION, {
    headers: { Authorization: `Bearer ${token}` },
    cache: 'no-store'
  });
  if (response.status === 401 || response.status === 403) return null;
  if (!response.ok) throw new Error('The Guild could not verify the key just now.');
  lastSessionValidation = Date.now();
  return response.json();
}

async function revalidateOnResume() {
  if (!accessToken || resumeValidationInFlight || !isOnline()) return;
  if (Date.now() - lastSessionValidation < RESUME_REVALIDATE_AFTER_MS) return;
  resumeValidationInFlight = true;
  try {
    const session = await verifySession(accessToken);
    if (!session) {
      await forgetStoredToken();
      showSignedOut('Your Guild key needs renewing.');
      return;
    }
    if (nativeShell.dataset.view === 'gate') showSignedIn(session);
  } catch {
    // A temporary road failure must never discard a valid stored key or open character.
    updateConnectionState();
  } finally {
    resumeValidationInFlight = false;
  }
}

async function restoreSession() {
  enter.disabled = true;
  setAuby('gate');
  setStatus('Auby is checking your Guild key…');
  try {
    await prepareSecureStorage();
    const stored = await SecureStorage.get(TOKEN_KEY);
    if (typeof stored !== 'string' || !stored) {
      showSignedOut();
      return;
    }

    const session = await verifySession(stored);
    if (!session) {
      await SecureStorage.remove(TOKEN_KEY);
      showSignedOut('Your Guild key needs renewing.');
      return;
    }

    accessToken = stored;
    showSignedIn(session);
  } catch {
    if (!isOnline()) {
      showSignedOut('The road to the Guild is offline. Your secure key has not been discarded.');
    } else {
      showSignedOut('Auby could not verify the secure Guild key. Please try again.');
    }
  } finally {
    enter.disabled = false;
  }
}

async function beginSignIn() {
  if (!isOnline()) { setStatus('The Guild Gate needs a connection.', 'error'); return; }
  enter.disabled = true;
  try {
    const verifier = randomValue(48);
    const state = randomValue(24);
    const challenge = base64url(await sha256(verifier));
    const response = await fetch(BEGIN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code_challenge: challenge, state })
    });
    if (!response.ok) throw new Error('The Guild could not begin native sign-in.');
    const data = await response.json();
    if (!data.authorize_url || data.callback_uri !== CALLBACK) {
      throw new Error('The Guild returned an unexpected native handoff.');
    }
    pending = { verifier, state };
    setStatus('Opening the Guild Gate…');
    await Browser.open({ url: data.authorize_url });
  } catch (error) {
    pending = null;
    setStatus(error instanceof Error ? error.message : 'Native sign-in could not begin.', 'error');
  } finally {
    enter.disabled = false;
  }
}

async function completeSignIn(url) {
  if (!url.startsWith(CALLBACK) || !pending) return;
  await Browser.close().catch(() => {});
  const parsed = new URL(url);
  const code = parsed.searchParams.get('code');
  const state = parsed.searchParams.get('state');
  if (!code || state !== pending.state) {
    pending = null;
    setStatus('The Guild Gate returned an invalid sign-in state.', 'error');
    return;
  }

  const verifier = pending.verifier;
  pending = null;

  try {
    setStatus('Auby is sealing your Guild key…');
    const response = await fetch(TOKEN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code, code_verifier: verifier })
    });
    if (!response.ok) throw new Error('The Guild could not complete native sign-in.');

    const token = await response.json();
    const candidate = token.access_token || null;
    if (!candidate) throw new Error('No Pocket access token was returned.');

    const session = await verifySession(candidate);
    if (!session) throw new Error('The native session could not be verified.');

    await prepareSecureStorage();
    await SecureStorage.set(TOKEN_KEY, candidate);
    accessToken = candidate;
    showSignedIn(session);
  } catch (error) {
    if (accessToken) {
      await fetch(REVOKE, {
        method: 'POST',
        headers: { Authorization: `Bearer ${accessToken}` }
      }).catch(() => {});
    }
    await forgetStoredToken();
    showSignedOut();
    setStatus(error instanceof Error ? error.message : 'Native sign-in failed.', 'error');
  }
}

async function signOut() {
  const token = accessToken;
  accessToken = null;
  pending = null;

  if (token) {
    await fetch(REVOKE, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` }
    }).catch(() => {});
  }

  await forgetStoredToken();
  showSignedOut('The Guild key has been returned to Auby.');
}

enter.addEventListener('click', beginSignIn);
openRegister.addEventListener('click', openAdventurersRegister);
registerBack.addEventListener('click', () => showView('gate'));
registerRefresh.addEventListener('click', openAdventurersRegister);
characterBack.addEventListener('click', () => showView('register'));
leave.addEventListener('click', signOut);
gatePrivacy.addEventListener('click', () => openExternalPage(PRIVACY_URL));
gateSupport.addEventListener('click', () => openExternalPage(SUPPORT_URL));
App.addListener('appUrlOpen', event => completeSignIn(event.url));
App.addListener('appStateChange', ({ isActive }) => { if (isActive) revalidateOnResume(); });
window.addEventListener('offline', () => updateConnectionState());
window.addEventListener('online', () => { updateConnectionState(); revalidateOnResume(); });
updateConnectionState();
installPullToRefresh(registerView, openAdventurersRegister);
installPullToRefresh(characterView, refreshOpenCharacter);

restoreSession();
