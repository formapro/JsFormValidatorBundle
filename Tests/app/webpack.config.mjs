// This project uses "Yarn" package manager for managing JavaScript dependencies along
// with "Webpack Encore" library that helps working with the CSS and JavaScript files
// that are stored in the "assets/" directory.
//
// Read https://symfony.com/doc/current/frontend.html to learn more about how
// to manage CSS and JavaScript files in Symfony applications.
import Encore from '@symfony/webpack-encore';

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    // when versioning is enabled, each filename will include a hash that changes
    // whenever the contents of that file change. This allows you to use aggressive
    // caching strategies. Use Encore.isProduction() to enable it only for production.
    .enableVersioning(false)
    .addEntry('app', './assets/js/app.js')
    .addEntry('FpJsFormElement', './public/bundles/fpjsformvalidator/js/FpJsFormValidatorWithJqueryInit.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .enableIntegrityHashes(Encore.isProduction())
    // Babel 8 removed the "useBuiltIns"/"corejs" options from @babel/preset-env,
    // so core-js polyfills are now injected by babel-plugin-polyfill-corejs3.
    .configureBabel((babelConfig) => {
        babelConfig.plugins.push([
            'babel-plugin-polyfill-corejs3',
            {
                method: 'usage-global',
                version: '3.50',
            },
        ]);
    })
;

export default await Encore.getWebpackConfig();
