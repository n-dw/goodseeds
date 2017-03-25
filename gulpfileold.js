// package vars
const pkg = require('./package.json');

//load gulp
var gulp = require('gulp');

// load all plugins in 'devDependencies' into the variable $
const $ = require('gulp-load-plugins')({
        pattern: ['*'],
        scope: ['devDependencies']
    });




function watch() {
    gulp.watch(paths.templates.src, ['templates', bs.reload]);
    gulp.watch(paths.js.src, ['scripts', 'lint']);
    gulp.watch([paths.scss.src.abby, paths.scss.src.site, paths.scss.src.resume], ['scss']);
    gulp.watch(paths.images.src, ['assets']);
}



// scss - build the scss to the build folder, including the required paths, and writing out a sourcemap
gulp.task('scss', () => {
    $.fancyLog("-> Compiling scss: " + pkg.paths.build.css + pkg.vars.scssName);
    return gulp.src(pkg.paths.src.scss + pkg.vars.scssName)
        .pipe($.plumber({ errorHandler: onError }))
        .pipe($.sourcemaps.init())
        .pipe($.sass({
                includePaths: pkg.paths.scss
            })
            .on('error', $.sass.logError))
        .pipe($.cached('sass_compile'))
        .pipe($.autoprefixer())
        .pipe($.sourcemaps.write('./'))
        .pipe($.size({ gzip: true, showFiles: true }))
        .pipe(gulp.dest(pkg.paths.build.css));
});

/*
BUILD TOOLS

-build  css - comp sass auto prefeix source map, cssnano on prod'

- optimige images - in src to build moxjpg, optipng

- 
watch, js, twig, scss, impages,

reload broweser on js, twig,scss,




/// JS hint on js watch
//scss lint on scss watch
 */


// fontello glyphs
gulp.task('fontelloIcons', () => {
  return gulp.src(pkg.paths.src.fontello + 'config.json')
    .pipe($.fontello())
    .pipe($.print())
    .pipe(gulp.dest(pkg.paths.build.fonts))
});


//favicons task
gulp.task('favicons', () => {
    return gulp.src(pkg.paths.favicon.src)
    .pipe($.favicons(
        appName: pkg.appInfo.name,
        appDescription: pkg.appInfo.description,
        background: pkg.appInfo.colour.mainBackgound,
        path: pkg.paths.dist.img,
        url: pkg.appInfo.url,
        display: "standalone",
        orientation: "portrait",
        version: pkg.version,
        html: pkg.paths.faviconTemplate,
        replace: true,
        icons: {
            android: true,
            appleIcon: true,
            appleStartup: false,
            coast: true,
            favicons: true,
            firefox: true,
            opengraph: true,
            twitter: false,
            windows: true,
            yandex: true
        }))
    .pipe(dulp.dest(pkg.paths.build.img));
});

/*BUILD ASSETS FILES

backup dist css, js, fonts, and img in a tmp first remove old from tmp and move build sources to dist is newer and different

*/
gulp.task('buidldToDist', () =>{
    return true;

});