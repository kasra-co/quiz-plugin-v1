'use strict';

var gulp = require( 'gulp' );
var gutil = require( 'gulp-util' );
var watchify = require( 'watchify' );
var browserify = require( 'browserify' );
var source = require( 'vinyl-source-stream' );
var buffer = require( 'vinyl-buffer' );
var sourcemaps = require( 'gulp-sourcemaps' );
var uglify = require( 'gulp-uglify' );
var sass = require( 'gulp-sass' );
var _ = require( 'lodash' );
var del = require( 'del' );

gulp.task( 'watch', [ 'sass', 'config', 'php' ], function() {
	gulp.watch( 'src/style/**/*.scss', [ 'sass' ]);
	gulp.watch( 'src/plugin/**/*.php', [ 'php' ]);
	gulp.watch( 'src/plugin/config/**/*', [ 'config' ]);
	bundle();
});

gulp.task( 'sass', function() {
	gulp.src( 'src/style/index.scss' )
	.pipe( sass() )
	.pipe( gulp.dest( 'dist/static' ));
});

gulp.task( 'php', function() {
	gulp.src( 'src/plugin/**/*.php' )
	.pipe( gulp.dest( 'dist' ));
});

gulp.task( 'config', function() {
	gulp.src( 'src/plugin/config/**/*' )
	.pipe( gulp.dest( 'dist/config' ));
});

gulp.task( 'clean', function() {
	del([ 'dist' ]);
});

// Watchify helps Browserify to only rebuild the parts of a source tree that are affected by a change, to reduce build time.
// See https://github.com/gulpjs/gulp/blob/master/docs/recipes/fast-browserify-builds-with-watchify.md
var bundler = watchify( browserify(
	'./src/app/index.js',
	_.extend(
		watchify.args,
		{ debug: true }
	)
));

bundler.transform( 'reactify' );
bundler.transform( 'es6ify' );

bundler.on( 'update', bundle ); // On any dependency update, run the bundler
bundler.on( 'log', gutil.log ); // Help bundler log to the terminal

function bundle() {
	return bundler.bundle()
	.on( 'error', gutil.log.bind( gutil, 'Browserify error' )) // Log errors during build
	.pipe( source( 'index.min.js' ))
	.pipe( gulp.dest( './dist/static' ));
}
