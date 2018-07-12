//Bundle assets
const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');
const path = require('path');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js and web/build/app.css
    .addEntry('app', './assets/js/app.js')
    .addEntry('diarypage', './assets/js/diarypage.js')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    .createSharedEntry('vendor', [
        'moment',
        'typeahead.js',
        'dropzone',
        'foundationjs',
        '@fancyapps/fancybox',
        'router'
    ])

    // allow sass/scss files to be processed
    .enableSassLoader(function(options) {
        options.includePaths = ['./vendor/zurb/foundation/scss'];
    })

    .addPlugin(new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/))
;

// generate the onfiguration
var config = Encore.getWebpackConfig();

//Alias js dependencies inside PHP deps folder
config.resolve.alias['foundationjs'] = path.resolve(__dirname, 'vendor/zurb/foundation/js/foundation');
config.resolve.alias['router$'] = path.resolve(__dirname, 'vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js');

//Allow jQuery from CDN to be used inside js modules
config.externals['jquery'] = 'jQuery';

//export config
module.exports = config;