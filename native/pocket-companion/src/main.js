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
  gateView.hidden = view !== 'gate';
  registerView.hidden = view !== 'register';
  characterView.hidden = view !== 'character';
};

const escapeText = value => String(value ?? '')
  .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
  .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

const signedModifier = value => Number(value) >= 0 ? `+${Number(value)}` : String(Number(value));

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

function openCharacter(id) {
  const character = liveCharacters.find(candidate => candidate.id === id);
  if (!character) return;
  characterTitle.textContent = character.name;
  characterLedger.innerHTML = `<div class="hero-portrait">${portraitMarkup(character)}</div>
    <p class="character-subtitle">Level ${escapeText(character.level)} ${escapeText(character.race)} ${escapeText(character.class)}</p>
    <div class="vital-row">
      <div><span>Current HP</span><strong>${escapeText(character.hp?.current)}</strong></div>
      <div><span>Maximum HP</span><strong>${escapeText(character.hp?.maximum)}</strong></div>
      <div><span>Temp HP</span><strong>${escapeText(character.hp?.temporary)}</strong></div>
    </div>
    <div class="measure-grid">
      <div><span>Armour Class</span><strong>${escapeText(character.armour_class)}</strong></div>
      <div><span>Initiative</span><strong>${escapeText(character.initiative)}</strong></div>
      <div><span>Speed</span><strong>${escapeText(character.speed_feet)} ft</strong></div>
      <div><span>Proficiency</span><strong>${signedModifier(character.proficiency_bonus)}</strong></div>
    </div>
    <div class="ability-grid">${Object.entries(character.abilities || {}).map(([ability, score]) =>
      `<div><span>${escapeText(ability)}</span><strong>${escapeText(score)}</strong></div>`).join('')}</div>
    <p class="ledger-note">This first native ledger is read-only. Its values come directly from the authoritative Companion Pocket API.</p>`;
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
