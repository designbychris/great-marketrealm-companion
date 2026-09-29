import { App } from '@capacitor/app';
import { Browser } from '@capacitor/browser';
import { Capacitor } from '@capacitor/core';
import { SecureStorage } from '@aparajita/capacitor-secure-storage';

const ORIGIN = 'https://greatmarketrealm.co.uk';
const BEGIN = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/begin`;
const TOKEN = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/token`;
const SESSION = `${ORIGIN}/wp-json/gmrc-pocket/v1/session`;
const REVOKE = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/revoke`;
const CALLBACK = 'uk.co.greatmarketrealm.pocket://auth/callback';
const TOKEN_KEY = 'access-token';
const STORAGE_PREFIX = 'gmrc_pocket_';

const enter = document.querySelector('#enter-guild');
const leave = document.querySelector('#leave-guild');
const status = document.querySelector('#native-status');
const auby = document.querySelector('#auby-state');

let pending = null;
let accessToken = null;

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

const showSignedOut = (message = 'Not connected to the Guild.') => {
  accessToken = null;
  pending = null;
  setAuby('gate');
  enter.hidden = false;
  leave.hidden = true;
  setStatus(message);
};

const showSignedIn = session => {
  setAuby('success');
  enter.hidden = true;
  leave.hidden = false;
  setStatus(
    `The Keeper remembers your key. Welcome, ${session.user?.display_name || 'adventurer'}!`,
    'success'
  );
};

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
leave.addEventListener('click', signOut);
App.addListener('appUrlOpen', event => completeSignIn(event.url));

restoreSession();
