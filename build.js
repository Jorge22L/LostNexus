// build.js
import { $ } from 'bun';

// 1. Copiar Chart.js desde node_modules
await $`cp node_modules/chart.js/dist/chart.umd.js public/js/chart.js`;

// 2. Procesar tus archivos JavaScript con Bun
await Bun.build({
  entrypoints: ['./public/js/main.js'], // Tu archivo principal JS
  outdir: './public/js',
  minify: process.env.NODE_ENV === 'production',
  naming: '[name].min.js' // Generará main.min.js
});

// 3. Opcional: Procesar charts.js si es necesario
await Bun.build({
  entrypoints: ['./public/js/charts.js'],
  outdir: './public/js',
  minify: process.env.NODE_ENV === 'production',
  naming: '[name].min.js' // Generará charts.min.js
});