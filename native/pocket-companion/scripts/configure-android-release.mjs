import { readFile, writeFile } from 'node:fs/promises';

const release = JSON.parse(await readFile(new URL('../config/release.json', import.meta.url), 'utf8'));
const gradleUrl = new URL('../android/app/build.gradle', import.meta.url);
let gradle = await readFile(gradleUrl, 'utf8');

if (!Number.isInteger(release.versionCode) || release.versionCode < 1) throw new Error('release.json versionCode must be a positive integer.');
if (!/^\d+\.\d+\.\d+(?:[-+][0-9A-Za-z.-]+)?$/.test(release.versionName)) throw new Error('release.json versionName must be a release version such as 1.0.0.');

gradle = gradle.replace(/versionCode\s+\d+/, `versionCode ${release.versionCode}`);
gradle = gradle.replace(/versionName\s+["'][^"']+["']/, `versionName "${release.versionName}"`);

function findNamedBlock(source, name, from = 0) {
  const matcher = new RegExp(`\\b${name}\\s*\\{`, 'g');
  matcher.lastIndex = from;
  const match = matcher.exec(source);
  if (!match) return null;
  const open = source.indexOf('{', match.index);
  let depth = 0;
  for (let i = open; i < source.length; i += 1) {
    if (source[i] === '{') depth += 1;
    if (source[i] === '}') {
      depth -= 1;
      if (depth === 0) return { start: match.index, open, close: i };
    }
  }
  return null;
}

const marker = '// GMRC_RELEASE_SIGNING';
const androidBlock = findNamedBlock(gradle, 'android');
if (!androidBlock) throw new Error('Could not locate android block in generated app/build.gradle.');

if (!gradle.includes(marker)) {
  const signing = `\n    ${marker}\n    signingConfigs {\n        release {\n            def storePath = System.getenv("GMRC_UPLOAD_STORE_FILE")\n            if (storePath) {\n                storeFile file(storePath)\n                storePassword System.getenv("GMRC_UPLOAD_STORE_PASSWORD")\n                keyAlias System.getenv("GMRC_UPLOAD_KEY_ALIAS")\n                keyPassword System.getenv("GMRC_UPLOAD_KEY_PASSWORD")\n            }\n        }\n    }\n`;
  gradle = gradle.slice(0, androidBlock.open + 1) + signing + gradle.slice(androidBlock.open + 1);
}

// III.M.7A.3: generated Android projects may already contain the malformed
// 7A signing line. Remove every previously-generated assignment first, then
// place exactly one assignment inside buildTypes.release below.
gradle = gradle.replace(/^\s*signingConfig\s+signingConfigs\.release\s*$/gm, '');

const buildTypesBlock = findNamedBlock(gradle, 'buildTypes');
if (!buildTypesBlock) throw new Error('Could not locate buildTypes block in generated app/build.gradle.');
const releaseBlock = findNamedBlock(gradle, 'release', buildTypesBlock.open + 1);
if (!releaseBlock || releaseBlock.start > buildTypesBlock.close) throw new Error('Could not locate release buildType in generated app/build.gradle.');

const releaseBody = gradle.slice(releaseBlock.open + 1, releaseBlock.close);
if (!releaseBody.includes('signingConfig signingConfigs.release')) {
  gradle = gradle.slice(0, releaseBlock.close) + '        signingConfig signingConfigs.release\n    ' + gradle.slice(releaseBlock.close);
}

await writeFile(gradleUrl, gradle);
console.log(`Pocket release configured: ${release.versionName} (${release.versionCode}).`);
