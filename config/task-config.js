const critical = require('critical');
const favicon = require('gulp-favicons');
const fancyLog = require('fancy-log');
const chalk = require('chalk');
const path  = require('path');


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
                   const criticaDestDir = path.resolve(process.env.PWD, PATH_CONFIG.temp);
                   const criticalSrc = PATH_CONFIG.urls.critical + element.url;
                   const criticalDest =  path.resolve(process.env.PWD, PATH_CONFIG.templates + element.template + '_critical.min.css');
                   fancyLog("-> Generating critical CSS: " + chalk.cyan(criticalSrc) + " -> " + chalk.magenta(criticalDest));

                   critical.generate({
                        src: criticalSrc,
                        dest: criticalDest,
                        inline: false,
                        ignore: [],
                        base: criticaDestDir,

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
            gulp.task('moveResources', () => {
                fancyLog("-> Moving build resources");
                //rememberthis for any additional taks the paths
                const paths = {
                    src: path.resolve(process.env.PWD, PATH_CONFIG.nodeModules),
                    dest: path.resolve(process.env.PWD, PATH_CONFIG.javascripts.destBuild),
                }
                PATH_CONFIG.resources.forEach(function(element) {
                    const resourceSrc =  path.resolve(paths.src, element.path);
                    const criticalDest = PATH_CONFIG.templates + element.template + '_critical.min.css';
                    fancyLog("-> Moving File " + chalk.cyan(element.name) + " FROM ->" + chalk.green(resourceSrc) + " TO -> " + chalk.magenta(paths.dest));

                    gulp.src(resourceSrc).pipe(gulp.dest(paths.dest));
                });
            });
            gulp.task('faviconGen', () => {
                fancyLog("-> Generating favicons");

                //rememberthis for any additional taks the paths
                const pathsFavIcon = {
                    src: path.resolve(process.env.PWD, PATH_CONFIG.src, PATH_CONFIG.images.src, PATH_CONFIG.favicon.src),
                    dest: path.resolve(process.env.PWD, PATH_CONFIG.favicon.dest),
                    htmlBuild: path.resolve(process.env.PWD, PATH_CONFIG.templates, PATH_CONFIG.favicon.html),
                }

                return gulp.src(pathsFavIcon.src).pipe(favicon({
                    appName: PATH_CONFIG.appInfo.name,
                    appDescription: PATH_CONFIG.appInfo.description,
                    developerName: PATH_CONFIG.appInfo.author,
                    developerURL: PATH_CONFIG.urls.live,
                    background: "#FFFFFF",
                    path:  PATH_CONFIG.favicon.relPath,
                    url: PATH_CONFIG.appInfo.siteUrl,
                    display: "standalone",
                    orientation: "portrait",
                    version: PATH_CONFIG.appInfo.version,
                    logging: false,
                    online: false,
                    html: pathsFavIcon.htmlBuild + "/favicons.html",
                    replace: true,
                    icons: {
                        android: true, // Create Android homescreen icon. `boolean`
                        appleIcon: true, // Create Apple touch icons. `boolean`
                        appleStartup: true, // Create Apple startup images. `boolean`
                        coast: true, // Create Opera Coast icon. `boolean`
                        favicons: true, // Create regular favicons. `boolean`
                        firefox: true, // Create Firefox OS icons. `boolean`
                        opengraph: true, // Create Facebook OpenGraph image. `boolean`
                        twitter: false, // Create Twitter Summary Card image. `boolean`
                        windows: true, // Create Windows 8 tile icons. `boolean`
                        yandex: true // Create Yandex browser icon. `boolean`
                    }
                } , (err, output) => {
                if (err) {
                    fancyLog(chalk.magenta(err));
                }
            })).pipe(gulp.dest(pathsFavIcon.dest));
            });
        },
        development: {
            prebuild: false,
            postbuild: false
        },
        production: {
            prebuild: false,
            postbuild: ['faviconGen','criticalcss', 'moveResources']
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
