import { readFile, writeFile } from 'node:fs/promises';

const release = JSON.parse(await readFile(new URL('../config/release.json', import.meta.url), 'utf8'));
const gradleUrl = new URL('../android/app/build.gradle', import.meta.url);
let gradle = await readFile(gradleUrl, 'utf8');

if (!Number.isInteger(release.versionCode) || release.versionCode < 1) throw new Error('release.json versionCode must be a positive integer.');
if (!/^\d+\.\d+\.\d+(?:[-+][0-9A-Za-z.-]+)?$/.test(release.versionName)) throw new Error('release.json versionName must be a release version such as 1.0.0.');

gradle = gradle.replace(/versionCode\s+\d+/, `versionCode ${release.versionCode}`);
gradle = gradle.replace(/versionName\s+["'][^"']+["']/, `versionName "${release.versionName}"`);

const marker = '// GMRC_RELEASE_SIGNING';
if (!gradle.includes(marker)) {
  const androidOpen = gradle.indexOf('android {');
  if (androidOpen === -1) throw new Error('Could not locate android block in generated app/build.gradle.');
  const insertAt = androidOpen + 'android {'.length;
  const signing = `\n    ${marker}\n    signingConfigs {\n        release {\n            def storePath = System.getenv("GMRC_UPLOAD_STORE_FILE")\n            if (storePath) {\n                storeFile file(storePath)\n                storePassword System.getenv("GMRC_UPLOAD_STORE_PASSWORD")\n                keyAlias System.getenv("GMRC_UPLOAD_KEY_ALIAS")\n                keyPassword System.getenv("GMRC_UPLOAD_KEY_PASSWORD")\n            }\n        }\n    }\n`;
  gradle = gradle.slice(0, insertAt) + signing + gradle.slice(insertAt);
}

const releaseBlock = /release\s*\{([\s\S]*?)\n\s*\}/m;
const match = gradle.match(releaseBlock);
if (!match) throw new Error('Could not locate release buildType in generated app/build.gradle.');
if (!match[1].includes('signingConfig signingConfigs.release')) {
  gradle = gradle.replace(releaseBlock, (whole, body) => whole.replace(body, `${body}\n            signingConfig signingConfigs.release`));
}

await writeFile(gradleUrl, gradle);
console.log(`Pocket release configured: ${release.versionName} (${release.versionCode}).`);
