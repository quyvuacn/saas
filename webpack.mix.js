const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
const mods = ['Admin', 'Merchant'];

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mods.map((mod) => {
    mix.js('Modules/' + mod + '/Resources/assets/js/app.js', 'js/' + mod.toLowerCase() + '.js')
        .sass('Modules/' + mod + '/Resources/assets/sass/app.scss', 'css/' + mod.toLowerCase() + '.css');
});

if (mix.inProduction()) {
    mix.version();
}
