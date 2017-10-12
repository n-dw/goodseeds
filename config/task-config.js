const critical = require('critical');
const fancyLog = require('fancy-log');
const chalk = require('chalk');


module.exports = {
    images: true,
    fonts: true,
    svgSprite: true,
    stylesheets: true,
    ghPages: false,
    html: false,
    static: false,

    additionalTasks: {
        initialize(gulp, PATH_CONFIG, TASK_CONFIG) {
            gulp.task('criticalcss', ['stylesheets'], () => {
               PATH_CONFIG.critical.forEach(function(element) {
                   const criticalSrc = PATH_CONFIG.urls.critical + element.url;
                   const criticalDest = PATH_CONFIG.templates + element.template + '_critical.min.css';
                   fancyLog("-> Generating critical CSS: " + chalk.cyan(criticalSrc) + " -> " + chalk.magenta(criticalDest));

                   critical.generate({
                        src: criticalSrc,
                        dest: criticalDest,
                        inline: false,
                        ignore: [],
                        base: '../public',

                        minify: true,
                        width: 1600,
                        height: 1200
                    }, (err, output) => {
                       if (err) {
                           fancyLog(chalk.magenta(err));
                       }
                   });
                });
            });
        },
        development: {
            prebuild: false,
            postbuild: false
        },
        production: {
            prebuild: false,
            postbuild: ['criticalcss']
        }
    },

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
