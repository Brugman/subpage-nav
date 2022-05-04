/**
 * timbr.dev Gulp template.
 *
 * Template last updated: 2021-06-17.
 * File last updated:     2022-05-04.
 */

/**
 * Directories.
 */
var dir = {
    input: {
        less: '.',
    },
    output: {
        less: '.',
    },
};

/**
 * Packages.
 */
var gulp         = require( 'gulp' );
var autoprefixer = require( 'gulp-autoprefixer' );
var cleancss     = require( 'gulp-clean-css' );
var filter       = require( 'gulp-filter' );
var gulpif       = require( 'gulp-if' );
var livereload   = require( 'gulp-livereload' );
var notify       = require( 'gulp-notify' );
var plumber      = require( 'gulp-plumber' );
var rename       = require( 'gulp-rename' );
var sourcemaps   = require( 'gulp-sourcemaps' );
var argv         = require( 'minimist' )( process.argv.slice( 2 ) );
var log          = require( 'fancy-log' );
// less
var less         = require( 'gulp-less' );

/**
 * Error handlers.
 */
var onErrorLess = function ( err ) {
    log( '------------------' );
    log( 'Less has an error!' );
    log( '------------------' );

    notify.onError({
        title: "Error in "+err.filename.replace( /^.*[\\\/]/, '' )+" on line "+err.line,
        message: err.extract,
        appID: "Gulp",
    })( err );

    log( '------------------' );

    this.emit('end');
};

/**
 * Procedures.
 */
var app = [];

app.processLess = function ( args ) {
    // use all the files
    return gulp.src( args.inputFiles )
        // catch errors
        .pipe( plumber( { errorHandler: onErrorLess } ) )
        // compile the less to css
        .pipe( less() )
        // autoprefix the css
        .pipe( autoprefixer() )
        // minify the css
        .pipe( cleancss( { keepSpecialComments: 0 } ) )
        // name the output file
        .pipe( rename( args.outputFile ) )
        // place the output file
        .pipe( gulp.dest( args.outputDir ) )
        // reload the site
        .pipe( livereload() );
};

/**
 * Tasks: Less.
 */
gulp.task( 'less_app', function ( done ) {
    app.processLess({
        'name'       : 'SPN less',
        'inputFiles' : [ dir.input.less+'/spn-backend.less' ],
        'outputDir'  : dir.output.less,
        'outputFile' : 'spn-backend.min.css',
    });
    done();
});

/**
 * Task: Watch.
 */
gulp.task( 'watch', function () {
    // Less
    gulp.watch( dir.input.less+'/**/*.less', gulp.parallel( 'less_app' ) );
    // notify
    gulp.src( 'node_modules/gulp-notify/test/fixtures/1.txt' ).pipe( notify({
        title: "Gulp watch is ready.",
        message: " ",
        appID: "Gulp",
    }));
});

/**
 * Task: Default.
 */
gulp.task( 'default', gulp.parallel(
    'less_app'
));

