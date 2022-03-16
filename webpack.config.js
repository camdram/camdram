//Bundle assets
const Encore = require("@symfony/webpack-encore");
const path = require("path");
const { spawn } = require("child_process");

spawn("node_modules/.bin/eslint", ["assets/js"], { stdio: "inherit" })
    .on('close', code => { if (code) throw "Linter failed" } );

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath("public/build/")

    // the public path used by the web server to access the previous directory
    .setPublicPath("/build")

    // creates the possible entrypoints, ONLY ONE used per page
    // If thinking of changing this reconsider .disableSingleRuntimeChunk()
    // default
    .addEntry("base", "./assets/js/base.js")
    // diary page
    .addEntry("diarypage", "./assets/js/diarypage.js")
    // venue pages, i.e. has a map on it.
    .addEntry("venue", "./assets/js/venues.js")
    .addEntry("ajax-forms", "./assets/js/ajax-forms.js")
    .addEntry("html-forms", "./assets/js/html-forms.js")
    .disableSingleRuntimeChunk()
    .splitEntryChunks()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    // allow sass/scss files to be processed
    .enableSassLoader(function (options) {
    })

    .configureBabel(babelConfig => {
        babelConfig.plugins.push("@babel/plugin-proposal-class-properties");
    })

    .configureCssLoader(config => {
        config.url = false;
    })
;

// generate the onfiguration
let config = Encore.getWebpackConfig();

//Alias js dependencies inside PHP deps folder
config.resolve.alias["router$"] = path.resolve(__dirname, "vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js");

//Allow jQuery from CDN to be used inside js modules
config.externals["jquery"] = "jQuery";

//export config
module.exports = config;
