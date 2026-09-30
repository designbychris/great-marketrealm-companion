import { access, cp, mkdir, readFile, readdir, rm, writeFile } from 'node:fs/promises';
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

// III.M.7A: version and signing policy are source-controlled; secrets remain outside Git.
execFileSync(process.execPath, [fileURLToPath(new URL('./configure-android-release.mjs', import.meta.url))], { stdio: 'inherit' });

// III.M.6C.2: install the canonical Auby launcher artwork after Capacitor sync.
// Generated Android projects are disposable; the source-controlled resources below are authoritative.
const launcherSource = new URL('../resources/android/', import.meta.url);
const launcherTarget = new URL('../android/app/src/main/res/', import.meta.url);
for (const entry of await readdir(launcherSource, { withFileTypes: true })) {
  if (!entry.isDirectory() || !entry.name.startsWith('mipmap-')) continue;
  const targetDir = new URL(`${entry.name}/`, launcherTarget);
  await mkdir(targetDir, { recursive: true });
  for (const oldName of ['ic_launcher.webp', 'ic_launcher_round.webp', 'ic_launcher_foreground.webp']) {
    await rm(new URL(oldName, targetDir), { force: true });
  }
  for (const name of ['ic_launcher.png', 'ic_launcher_round.png', 'ic_launcher_foreground.png']) {
    const source = new URL(`${entry.name}/${name}`, launcherSource);
    try { await access(source); } catch { continue; }
    await cp(source, new URL(name, targetDir));
  }
}

console.log('MarketRealm Pocket Android expedition is prepared with Auby at the Gate.');
