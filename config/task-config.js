module.exports = {
    images: true,
    fonts: true,
    svgSprite: true,
    stylesheets: true,
    ghPages: false,
    html: false,
    static: false,

    javascripts: {
        babel: {
            presets: [
                ['es2015', {'modules': false}],
                'stage-2'
            ]
        },
        entry: {
            // files paths are relative to
            // javascripts.dest in path-config.json
            app: ["./app.js"]
        },
        loaders: [{
            test: /\.vue$/,
            loader: 'vue-loader',
        },
        ],
        // This tells webpack middleware where to
        // serve js files from in development:
        publicPath: "/assets/js"
    },

    browserSync: {
        // Update this to match your development URL
        proxy: 'queef.dev',
        files: ['craft/templates/**/*']
    },

    production: {
        rev: true
    },


}

