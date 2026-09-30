import { access, readFile } from 'node:fs/promises';
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const required = ['GMRC_UPLOAD_STORE_FILE','GMRC_UPLOAD_STORE_PASSWORD','GMRC_UPLOAD_KEY_ALIAS','GMRC_UPLOAD_KEY_PASSWORD'];
const missing = required.filter((name) => !process.env[name]);
if (missing.length) {
  throw new Error(`Release signing is not configured. Missing: ${missing.join(', ')}. Keep these values outside Git.`);
}
await access(process.env.GMRC_UPLOAD_STORE_FILE);
const release = JSON.parse(await readFile(new URL('../config/release.json', import.meta.url), 'utf8'));
const android = fileURLToPath(new URL('../android/', import.meta.url));
const wrapper = process.platform === 'win32' ? 'gradlew.bat' : './gradlew';
execFileSync(process.execPath, [fileURLToPath(new URL('./configure-android-release.mjs', import.meta.url))], { stdio: 'inherit' });
execFileSync(wrapper, ['bundleRelease'], { cwd: android, stdio: 'inherit', shell: process.platform === 'win32' });
console.log(`Signed Pocket ${release.versionName} (${release.versionCode}) bundle created under android/app/build/outputs/bundle/release/.`);
