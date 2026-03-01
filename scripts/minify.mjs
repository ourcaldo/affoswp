/**
 * Simple CSS/JS minifier using Node.js built-in APIs.
 * No external dependencies required.
 *
 * Usage:
 *   node scripts/minify.mjs       # minify both CSS and JS
 *   node scripts/minify.mjs css   # minify CSS only
 *   node scripts/minify.mjs js    # minify JS only
 */

import { readFileSync, writeFileSync, statSync } from 'fs';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const root = resolve(__dirname, '..');

const files = {
    css: [
        { src: 'assets/css/main.css', dest: 'assets/css/main.min.css' },
    ],
    js: [
        { src: 'assets/js/main.js', dest: 'assets/js/main.min.js' },
        { src: 'assets/js/compare-page.js', dest: 'assets/js/compare-page.min.js' },
    ],
};

function minifyCSS(code) {
    return code
        // Remove comments (but keep /*! ... */ license comments)
        .replace(/\/\*(?!!)[^]*?\*\//g, '')
        // Remove whitespace around selectors/properties
        .replace(/\s*([{}:;,>~+])\s*/g, '$1')
        // Collapse multiple spaces/newlines
        .replace(/\s{2,}/g, ' ')
        // Remove leading/trailing whitespace per line
        .replace(/^\s+|\s+$/gm, '')
        // Remove empty lines
        .replace(/\n{2,}/g, '\n')
        // Remove trailing semicolons before }
        .replace(/;}/g, '}')
        // Final trim
        .trim();
}

function minifyJS(code) {
    return code
        // Remove single-line comments (but not URLs with //)
        .replace(/(?<![:'"])\/\/(?!['"]).*/g, '')
        // Remove multi-line comments (but keep /*! ... */ license comments)
        .replace(/\/\*(?!!)[^]*?\*\//g, '')
        // Collapse multiple newlines
        .replace(/\n{2,}/g, '\n')
        // Remove leading whitespace on each line
        .replace(/^\s+/gm, '')
        // Remove empty lines
        .replace(/\n{2,}/g, '\n')
        // Final trim
        .trim();
}

function formatSize(bytes) {
    return (bytes / 1024).toFixed(1) + ' KB';
}

function processType(type) {
    const list = files[type] || [];
    const minifier = type === 'css' ? minifyCSS : minifyJS;

    for (const { src, dest } of list) {
        const srcPath = resolve(root, src);
        const destPath = resolve(root, dest);

        try {
            const content = readFileSync(srcPath, 'utf8');
            const minified = minifier(content);
            writeFileSync(destPath, minified, 'utf8');

            const srcSize = statSync(srcPath).size;
            const destSize = statSync(destPath).size;
            const savings = ((1 - destSize / srcSize) * 100).toFixed(1);

            console.log(`  ${src} → ${dest}`);
            console.log(`    ${formatSize(srcSize)} → ${formatSize(destSize)} (${savings}% smaller)`);
        } catch (err) {
            console.error(`  Error processing ${src}: ${err.message}`);
        }
    }
}

// Determine what to build
const arg = process.argv[2];
const types = arg ? [arg] : ['css', 'js'];

console.log('Minifying assets...\n');

for (const type of types) {
    console.log(`[${type.toUpperCase()}]`);
    processType(type);
    console.log('');
}

console.log('Done!');
