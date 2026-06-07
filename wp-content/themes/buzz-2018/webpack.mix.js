let mix = require("laravel-mix");

// The proxy for browser sync. Should match your localhost url for the project
let proxy = "https://thebuzz.fireflydigital.dev/demo1";

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your theme. Compile the Sass for your front-end and editor
 | css. Optionally compile JS here if you are using ES2016+ or web components
 |
 */
mix.setPublicPath("assets")
    .options({
        processCssUrls: false,
    })
    .sourceMaps()
    .webpackConfig({
        devtool: "source-map",

        // compile time warnings
        stats: {
            children: true,
        },
    })
    .js("resources/js/main.js", "js")
    .js("resources/js/lightcase.js", "js")
    .sass("resources/scss/main.scss", "css")
    .sass("resources/scss/editor.scss", "css")
    .sass("resources/scss/email.scss", "css")
    .browserSync({
        proxy: proxy,
        files: ["**/*.css", "**/*.php", "**/*.twig"],
    });
