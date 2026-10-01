import { access, readFile } from 'node:fs/promises';

const root = new URL('../', import.meta.url);
const release = JSON.parse(await readFile(new URL('config/release.json', root), 'utf8'));
const capacitor = JSON.parse(await readFile(new URL('capacitor.config.json', root), 'utf8'));
const environments = JSON.parse(await readFile(new URL('config/environments.json', root), 'utf8'));
const pkg = JSON.parse(await readFile(new URL('package.json', root), 'utf8'));

const failures = [];
const passes = [];
const check = (ok, message) => (ok ? passes : failures).push(message);

check(release.applicationId === 'uk.co.greatmarketrealm.pocket', 'Application ID is uk.co.greatmarketrealm.pocket');
check(release.versionName === '1.0.0' && release.versionCode === 1, 'Release identity is 1.0.0 (1)');
check(capacitor.appId === release.applicationId, 'Capacitor appId matches release identity');
check(capacitor.webDir === 'dist' && !capacitor.server?.url, 'Production bundle is local and has no remote server.url');
check(environments.production?.origin === 'https://greatmarketrealm.co.uk', 'Production origin is HTTPS Great MarketRealm');
check(environments.rules?.nativeCharacterDatabase === false, 'No native character database');
check(environments.rules?.offlineCharacterWrites === false, 'No offline character writes');
check(environments.rules?.wordpressPasswordInNativeApp === false, 'WordPress password is not handled by native app');
check(Boolean(pkg.dependencies?.['@aparajita/capacitor-secure-storage']), 'Secure storage dependency is present');

const android = new URL('android/', root);
try {
  await access(android);
  const variables = await readFile(new URL('variables.gradle', android), 'utf8');
  const target = Number(variables.match(/targetSdkVersion\s*=\s*(\d+)/)?.[1] ?? 0);
  const compile = Number(variables.match(/compileSdkVersion\s*=\s*(\d+)/)?.[1] ?? 0);
  check(target >= 36, `Android target SDK is ${target || 'unresolved'} (requires 36+)`);
  check(compile >= 36, `Android compile SDK is ${compile || 'unresolved'} (requires 36+)`);

  const manifest = await readFile(new URL('app/src/main/AndroidManifest.xml', android), 'utf8');
  check(manifest.includes('uk.co.greatmarketrealm.pocket') || manifest.includes('GMRC_NATIVE_CALLBACK'), 'Native auth callback is present');
  check(!/ACCESS_(FINE|COARSE|BACKGROUND)_LOCATION/.test(manifest), 'No location permission declared');
  check(!/RECORD_AUDIO|CAMERA|READ_CONTACTS|WRITE_CONTACTS/.test(manifest), 'No camera, microphone, or contacts permission declared');
  check(!/usesCleartextTraffic\s*=\s*["']true["']/.test(manifest), 'Cleartext traffic is not explicitly enabled');
} catch (error) {
  failures.push('Generated Android project is unavailable. Run npm run android:prepare before npm run release:audit.');
}

console.log('\nGreat MarketRealm Pocket — III.M.7C.5 Release Readiness Audit\n');
for (const item of passes) console.log(`PASS  ${item}`);
for (const item of failures) console.error(`FAIL  ${item}`);
console.log(`\n${passes.length} passed; ${failures.length} failed.`);
if (failures.length) process.exitCode = 1;
