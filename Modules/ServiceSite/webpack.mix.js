const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/servicesite.js')
    .postCss( __dirname + '/Resources/assets/css/app.css', 'css/servicesite.css', [
        require('tailwindcss'),
    ]);


if (mix.inProduction()) {
    mix.version();
}
