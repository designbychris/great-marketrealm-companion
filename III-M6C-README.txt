Phase III.M.6C — The First Native Expedition

Apply this patch at the Great MarketRealm Companion plugin root.

Server regression first:
  php vendor/bin/phpunit --display-warnings

Android development machine (Node.js 22+ and Android Studio required):
  cd native/pocket-companion
  npm install
  npm run build
  npm run android:prepare
  npm run cap:open:android

III.M.6C intentionally keeps the native bearer token in process memory only.
It does not persist the token in Local Storage or Preferences. Persistent sign-in
will only be added with reviewed Keystore/Keychain-backed storage.
