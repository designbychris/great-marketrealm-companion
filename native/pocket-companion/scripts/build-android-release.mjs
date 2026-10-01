import { access, readFile } from 'node:fs/promises';
import { spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const required = ['GMRC_UPLOAD_STORE_FILE','GMRC_UPLOAD_STORE_PASSWORD','GMRC_UPLOAD_KEY_ALIAS','GMRC_UPLOAD_KEY_PASSWORD'];
const missing = required.filter((name) => !process.env[name]);
if (missing.length) {
  throw new Error(`Release signing is not configured. Missing: ${missing.join(', ')}. Keep these values outside Git.`);
}

await access(process.env.GMRC_UPLOAD_STORE_FILE);

const release = JSON.parse(await readFile(new URL('../config/release.json', import.meta.url), 'utf8'));
const android = fileURLToPath(new URL('../android/', import.meta.url));
const configure = fileURLToPath(new URL('./configure-android-release.mjs', import.meta.url));

function redact(value) {
  let output = String(value ?? '');
  for (const name of ['GMRC_UPLOAD_STORE_PASSWORD', 'GMRC_UPLOAD_KEY_PASSWORD']) {
    const secret = process.env[name];
    if (secret) output = output.split(secret).join('[REDACTED]');
  }
  output = output
    .replace(/(storePassword=)[^,\]\}\r\n]+/gi, '$1[REDACTED]')
    .replace(/(keyPassword=)[^,\]\}\r\n]+/gi, '$1[REDACTED]');
  return output;
}

function emit(result) {
  if (result.stdout) process.stdout.write(redact(result.stdout));
  if (result.stderr) process.stderr.write(redact(result.stderr));
}

function run(command, args, options = {}) {
  const result = spawnSync(command, args, {
    cwd: options.cwd,
    env: options.env ?? process.env,
    encoding: 'utf8',
    windowsHide: true,
  });
  emit(result);
  if (result.error) throw result.error;
  if (result.status !== 0) {
    throw new Error(`${options.label ?? command} failed with exit code ${result.status}.`);
  }
}

function gradleArgs(task) {
  if (process.platform === 'win32') {
    return {
      command: process.env.ComSpec || 'cmd.exe',
      args: ['/d', '/s', '/c', `gradlew.bat ${task}`],
    };
  }
  return { command: './gradlew', args: [task] };
}

// Always repair/configure the disposable Android project immediately before release.
run(process.execPath, [configure], { label: 'Pocket release configuration' });

// Validate the Gradle model once without signing secrets. This catches malformed
// generated Gradle DSL before credentials are ever loaded into a SigningConfig.
const preflightEnv = { ...process.env };
for (const name of required) delete preflightEnv[name];
const preflight = gradleArgs('help');
run(preflight.command, preflight.args, {
  cwd: android,
  env: preflightEnv,
  label: 'Secret-free Gradle preflight',
});

// The signed build is captured and redacted before any diagnostics are emitted.
// This prevents Gradle object dumps from printing signing passwords to the console.
const bundle = gradleArgs('bundleRelease');
run(bundle.command, bundle.args, { cwd: android, label: 'Signed Android bundle' });

console.log(`Signed Pocket ${release.versionName} (${release.versionCode}) bundle created under android/app/build/outputs/bundle/release/.`);
