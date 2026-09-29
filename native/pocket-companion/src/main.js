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
const CALLBACK = 'uk.co.greatmarketrealm.pocket://auth/callback';
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

let pending = null;
let accessToken = null;
let liveCharacters = [];

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
      <div class="dice-result" aria-live="off"><span data-dice-label>Choose a trained roll</span><strong data-dice-total>—</strong><small data-dice-math>Tap an ability, saving throw or skill.</small></div>
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
    return `<img src="${escapeText(portrait.url)}" alt="Portrait of ${escapeText(character.name)}" />`;
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
    card.addEventListener('click', () => openCharacter(character.id));
    characterList.append(card);
  }
}

async function openAdventurersRegister() {
  showView('register');
  registerStatus.hidden = false;
  registerStatus.textContent = 'Auby is opening the Adventurers\' Register…';
  characterList.replaceChildren();
  try {
    const characters = await fetchCharacters();
    renderRegister(characters);
    registerStatus.textContent = `${characters.length} adventurer${characters.length === 1 ? '' : 's'} registered.`;
  } catch (error) {
    if (!accessToken) return;
    registerStatus.textContent = error instanceof Error ? error.message : 'The Register could not be opened.';
  }
}

async function persistVitality(character, body) {
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
      message.textContent = error instanceof Error ? error.message : 'Adventuring Measures could not be updated.';
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

function placeholderPanel(title, copy) {
  const panel = document.createElement('section');
  panel.className = 'native-panel native-coming-soon';
  panel.innerHTML = `<p class="eyebrow">Native Pocket</p><h2>${escapeText(title)}</h2><p>${escapeText(copy)}</p>`;
  return panel;
}

function openCharacter(id) {
  const character = liveCharacters.find(candidate => candidate.id === id);
  if (!character) return;
  characterTitle.textContent = character.name;
  characterLedger.replaceChildren();

  const hero = document.createElement('section');
  hero.className = 'native-character-hero';
  hero.innerHTML = `<div class="hero-portrait">${portraitMarkup(character)}</div>
    <div><p class="eyebrow">Adventurer Overview</p><h2>${escapeText(character.name)}</h2><p>Level ${escapeText(character.level)} ${escapeText(character.race)} ${escapeText(character.class)}</p></div>`;

  const overview = document.createElement('section');
  overview.className = 'native-dashboard-panel';
  overview.dataset.nativePanel = 'overview';
  overview.append(vitalityPanel(character));
  const measures = document.createElement('div');
  measures.className = 'measure-grid';
  measures.innerHTML = `<div><span>Armour Class</span><strong>${escapeText(character.armour_class)}</strong></div><div><span>Initiative</span><strong>${escapeText(character.initiative)}</strong></div><div><span>Speed</span><strong>${escapeText(character.speed_feet)} ft</strong></div><div><span>Proficiency</span><strong>${signedModifier(character.proficiency_bonus)}</strong></div>`;
  overview.append(measures);

  const characterPanel = document.createElement('section');
  characterPanel.className = 'native-dashboard-panel'; characterPanel.dataset.nativePanel = 'character'; characterPanel.hidden = true;
  characterPanel.innerHTML = `<div class="ability-grid">${Object.entries(character.abilities || {}).map(([ability, score]) => { const modifier = character.ability_modifiers?.[ability] ?? 0; return `<button type="button" data-ability-roll data-roll-label="${escapeText(ability)} Ability Check" data-roll-modifier="${escapeText(modifier)}"><span>${escapeText(ability)}</span><strong>${escapeText(score)}</strong><small>${escapeText(signedModifier(modifier))}</small></button>`; }).join('')}</div>`;
  characterPanel.querySelectorAll('[data-ability-roll]').forEach(button => button.addEventListener('click', () => performTrainingRoll(button.dataset.rollLabel, Number(button.dataset.rollModifier), 'ability')));
  characterPanel.append(trainingPanel(character));

  const combat = placeholderPanel('Combat', 'The native combat ledger will join the field in the next expedition.'); combat.classList.add('native-dashboard-panel'); combat.dataset.nativePanel = 'combat'; combat.hidden = true;
  const spells = placeholderPanel('Spellbook', 'The Pocket Spellbook remains safely in the Companion until its native phase.'); spells.classList.add('native-dashboard-panel'); spells.dataset.nativePanel = 'spells'; spells.hidden = true;
  const more = placeholderPanel('More', 'Equipment and further adventuring tools will gather here as the native dashboard grows.'); more.classList.add('native-dashboard-panel'); more.dataset.nativePanel = 'more'; more.hidden = true;

  const dock = document.createElement('nav');
  dock.className = 'native-dashboard-dock'; dock.setAttribute('aria-label', 'Pocket character navigation');
  for (const [key, label] of [['overview','Overview'],['character','Character'],['combat','Combat'],['spells','Spellbook'],['more','More']]) {
    const button = document.createElement('button'); button.type = 'button'; button.textContent = label; button.dataset.nativeTab = key; button.setAttribute('aria-current', key === 'overview' ? 'page' : 'false');
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
  const response = await fetch(SESSION, {
    headers: { Authorization: `Bearer ${token}` }
  });
  if (!response.ok) return null;
  return response.json();
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
    showSignedOut('Auby could not read the secure Guild key. Please enter the Guild again.');
  } finally {
    enter.disabled = false;
  }
}

async function beginSignIn() {
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
App.addListener('appUrlOpen', event => completeSignIn(event.url));

restoreSession();
