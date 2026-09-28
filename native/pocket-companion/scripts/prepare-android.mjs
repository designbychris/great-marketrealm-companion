import { access, readFile, writeFile } from 'node:fs/promises';
import { execFileSync } from 'node:child_process';

const androidDir = new URL('../android/', import.meta.url);
try { await access(androidDir); } catch {
  execFileSync(process.platform === 'win32' ? 'npx.cmd' : 'npx', ['cap', 'add', 'android'], { stdio: 'inherit' });
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
execFileSync(process.platform === 'win32' ? 'npx.cmd' : 'npx', ['cap', 'sync', 'android'], { stdio: 'inherit' });
console.log('MarketRealm Pocket Android expedition is prepared.');
