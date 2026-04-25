/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your theme assets. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

module.exports = (mix) => {
    // SASS
    mix.sass(
        'plugins/naekb/dadatawidgets/formwidgets/dadatasuggestions/assets/src/dadatasuggestions.scss',
        'plugins/naekb/dadatawidgets/formwidgets/dadatasuggestions/assets/css/dadatasuggestions.css'
    );
    // JS
    mix.js(
        'plugins/naekb/dadatawidgets/formwidgets/dadatasuggestions/assets/src/dadatasuggestions.js',
        'plugins/naekb/dadatawidgets/formwidgets/dadatasuggestions/assets/js/dadatasuggestions.js'
    );
};
