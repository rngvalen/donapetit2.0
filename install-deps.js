#!/usr/bin/env node
const { spawnSync } = require('child_process');
const { existsSync } = require('fs');
const path = require('path');

const projectRoot = __dirname;
const pkgFile = path.join(projectRoot, 'package.json');

if (!existsSync(pkgFile)) {
  console.error('[ERROR] Ejecuta este script desde la carpeta raiz del proyecto.');
  process.exit(1);
}

const npmCheck = spawnSync('npm', ['--version'], { stdio: 'ignore' });
if (npmCheck.error) {
  console.error('[ERROR] npm no se encuentra en el PATH. Instala Node.js desde https://nodejs.org/');
  process.exit(1);
}

console.log('Instalando dependencias con npm install...');
const install = spawnSync('npm', ['install'], { stdio: 'inherit' });
if (install.status !== 0) {
  console.error('[ERROR] La instalacion de dependencias fallo. Revisa el mensaje anterior.');
  process.exit(install.status || 1);
}

console.log('\nDependencias instaladas correctamente.');
console.log('Comandos utiles:');
console.log(' - npm run build  (compila estilos para produccion)');
console.log(' - npm run dev    (recompila estilos en modo observacion)');
