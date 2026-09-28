import { App } from '@capacitor/app';
import { Browser } from '@capacitor/browser';

const ORIGIN = 'https://greatmarketrealm.co.uk';
const BEGIN = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/begin`;
const TOKEN = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/token`;
const SESSION = `${ORIGIN}/wp-json/gmrc-pocket/v1/session`;
const REVOKE = `${ORIGIN}/wp-json/gmrc-pocket/v1/native/revoke`;
const CALLBACK = 'uk.co.greatmarketrealm.pocket://auth/callback';

const enter = document.querySelector('#enter-guild');
const leave = document.querySelector('#leave-guild');
const status = document.querySelector('#native-status');
let pending = null;
let accessToken = null; // III.M.6C: deliberately memory-only until secure persistence is introduced.

const setStatus = (message, kind = '') => {
  status.textContent = message;
  status.className = `status ${kind}`.trim();
};
const base64url = bytes => btoa(String.fromCharCode(...bytes)).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
const randomValue = length => base64url(crypto.getRandomValues(new Uint8Array(length)));
const sha256 = async value => new Uint8Array(await crypto.subtle.digest('SHA-256', new TextEncoder().encode(value)));

async function beginSignIn() {
  enter.disabled = true;
  try {
    const verifier = randomValue(48);
    const state = randomValue(24);
    const challenge = base64url(await sha256(verifier));
    const response = await fetch(BEGIN, {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code_challenge: challenge, state })
    });
    if (!response.ok) throw new Error('The Guild could not begin native sign-in.');
    const data = await response.json();
    if (!data.authorize_url || data.callback_uri !== CALLBACK) throw new Error('The Guild returned an unexpected native handoff.');
    pending = { verifier, state };
    setStatus('Opening the Guild Gate…');
    await Browser.open({ url: data.authorize_url });
  } catch (error) {
    pending = null;
    setStatus(error instanceof Error ? error.message : 'Native sign-in could not begin.', 'error');
  } finally { enter.disabled = false; }
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
    setStatus('Sealing the Guild token…');
    const response = await fetch(TOKEN, {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code, code_verifier: verifier })
    });
    if (!response.ok) throw new Error('The Guild could not complete native sign-in.');
    const token = await response.json();
    accessToken = token.access_token || null;
    if (!accessToken) throw new Error('No Pocket access token was returned.');
    const sessionResponse = await fetch(SESSION, { headers: { Authorization: `Bearer ${accessToken}` } });
    if (!sessionResponse.ok) throw new Error('The native session could not be verified.');
    const session = await sessionResponse.json();
    setStatus(`Guild Gate complete. Welcome, ${session.user?.display_name || 'adventurer'}!`, 'success');
    enter.hidden = true;
    leave.hidden = false;
  } catch (error) {
    accessToken = null;
    setStatus(error instanceof Error ? error.message : 'Native sign-in failed.', 'error');
  }
}

async function signOut() {
  if (accessToken) {
    await fetch(REVOKE, { method: 'POST', headers: { Authorization: `Bearer ${accessToken}` } }).catch(() => {});
  }
  accessToken = null;
  pending = null;
  enter.hidden = false;
  leave.hidden = true;
  setStatus('Not connected to the Guild.');
}

enter.addEventListener('click', beginSignIn);
leave.addEventListener('click', signOut);
App.addListener('appUrlOpen', event => completeSignIn(event.url));
