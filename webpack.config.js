// webpack.config.js
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableStimulusBridge('./assets/controllers.json')
    .enableSassLoader(() => {}, {
        resolveUrlLoader: false
    })
    .autoProvidejQuery() // Si vous avez besoin de jQuery pour certains plugins Bootstrap
    .enableIntegrityHashes()
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();