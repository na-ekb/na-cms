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
    mix.less('plugins/naekb/pages/assets/less/pages.less', 'plugins/naekb/pages/assets/css/');
    mix.less('plugins/naekb/pages/assets/less/treeview.less', 'plugins/naekb/pages/assets/css/');
    mix.js('plugins/naekb/pages/assets/src/js/meetings.js', 'plugins/naekb/pages/assets/js/');
    mix.js('plugins/naekb/pages/assets/src/js/jft.js', 'plugins/naekb/pages/assets/js/');
}
