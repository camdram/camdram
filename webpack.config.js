//Bundle assets
const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');
const path = require('path');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // creates the possible entrypoints, ONLY ONE used per page
    // If thinking of changing this reconsider .disableSingleRuntimeChunk()
    // default
    .addEntry('base', './assets/js/base.js')
    // diary page
    .addEntry('diarypage', './assets/js/diarypage.js')
    // venue pages, i.e. has a map on it.
    .addEntry('venue', './assets/js/venues.js')
    .addEntry('ajax-forms', './assets/js/ajax-forms.js')
    .disableSingleRuntimeChunk()
    .splitEntryChunks()

    // The Typeahead library is not compatible (it seems) with current Webpack
    // so is just included directly in every page.
    .copyFiles({from: './node_modules/typeahead.js/dist/',
        pattern: /typeahead\.bundle\.min\.js/,
        to: 'typeahead.[hash:8].js'})

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    // allow sass/scss files to be processed
    .enableSassLoader(function(options) {
    })

    .addPlugin(new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/))
;

// generate the onfiguration
var config = Encore.getWebpackConfig();

//Alias js dependencies inside PHP deps folder
config.resolve.alias['router$'] = path.resolve(__dirname, 'vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js');

//Allow jQuery from CDN to be used inside js modules
config.externals['jquery'] = 'jQuery';

//export config
module.exports = config;
