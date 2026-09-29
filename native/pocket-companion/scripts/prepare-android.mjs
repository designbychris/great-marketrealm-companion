import { access, readFile, writeFile } from 'node:fs/promises';
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const androidDir = new URL('../android/', import.meta.url);
const capacitorCli = fileURLToPath(new URL('../node_modules/@capacitor/cli/bin/capacitor', import.meta.url));

function runCapacitor(...args) {
  execFileSync(process.execPath, [capacitorCli, ...args], { stdio: 'inherit' });
}

try { await access(androidDir); } catch {
  runCapacitor('add', 'android');
}

const manifestUrl = new URL('../android/app/src/main/AndroidManifest.xml', import.meta.url);
let manifest = await readFile(manifestUrl, 'utf8');
const marker = '<!-- GMRC_NATIVE_CALLBACK -->';
if (!manifest.includes(marker)) {
  const intent = `\n            ${marker}\n            <intent-filter>\n                <action android:name="android.intent.action.VIEW" />\n                <category android:name="android.intent.category.DEFAULT" />\n                <category android:name="android.intent.category.BROWSABLE" />\n                <data android:scheme="uk.co.greatmarketrealm.pocket" android:host="auth" android:path="/callback" />\n            </intent-filter>`;
  const activityEnd = manifest.indexOf('</activity>');
  if (activityEnd === -1) throw new Error('Could not locate MainActivity in AndroidManifest.xml');
  manifest = manifest.slice(0, activityEnd) + intent + '\n        ' + manifest.slice(activityEnd);
  await writeFile(manifestUrl, manifest);
}

runCapacitor('sync', 'android');
console.log('MarketRealm Pocket Android expedition is prepared.');
