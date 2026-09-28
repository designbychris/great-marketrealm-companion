import { access } from 'node:fs/promises';

const required = [
  new URL('../www/index.html', import.meta.url),
  new URL('../www/workshop.css', import.meta.url),
  new URL('../config/environments.json', import.meta.url),
];

for (const file of required) await access(file);
console.log('MarketRealm Pocket native workshop web bundle is ready.');
