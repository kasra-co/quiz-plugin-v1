'use strict';

var gulp = require( 'gulp' );
var gutil = require( 'gulp-util' );
var watchify = require( 'watchify' );
var browserify = require( 'browserify' );
var source = require( 'vinyl-source-stream' );
var buffer = require( 'vinyl-buffer' );
var sass = require( 'gulp-ruby-sass' );
var _ = require( 'lodash' );
var del = require( 'del' );
var rev = require( 'gulp-rev' );
var revDel = require( 'rev-del' );
var rename = require( 'gulp-rename' );

gulp.task( 'watch', [ 'sass', 'font', 'config', 'php' ], function() {
	gulp.watch( 'node_modules/quiz/style/**/*.scss', [ 'sass' ]);
	gulp.watch( 'src/plugin/**/*.php', [ 'php' ]);
	gulp.watch( 'src/plugin/config/**/*', [ 'config' ]);

	buildJs();
});

gulp.task( 'build', [ 'sass', 'font', 'config', 'php' ], function() { buildJs( true ); });

gulp.task( 'sass', function() {
	buildSass( 'quiz-app.min', 'node_modules/quiz/style/index.scss' );
	buildSass( 'quiz-editor.min', 'node_modules/quiz-editor/style/index.scss' );
});

gulp.task( 'font', function() {
	gulp.src( 'node_modules/quiz-editor/font/**/*' )
	.pipe( gulp.dest( 'dist/font' ));
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

function builder( name, entry, isDev ) {
	var bundler;

	if( isDev ) {
		bundler = watchify( browserify(
			entry,
			_.extend( watchify.args, { debug: true })
		));
	} else {
		bundler = browserify( entry );
	}

	bundler.transform( 'reactify' );
	bundler.transform( 'es6ify' );
	bundler.transform({ global: true }, 'uglifyify' );

	bundler.on( 'log', gutil.log ); // Help bundler log to the terminal
	bundler.on( 'error', gutil.log.bind( gutil, 'Browserify error' ));

	function bundle( dest, filename ) {
		bundler.on( 'update', nextBundle );

		function nextBundle() {
			gutil.log( 'rebundling' );
			return bundler.bundle()
			.on( 'error', gutil.log.bind( gutil, 'Browserify error' )) // Log errors during build
			.pipe( source( filename ))
			.pipe( buffer() )
			.pipe( rev() )
			.pipe( gulp.dest( dest ))
			.pipe( rev.manifest( 'js-manifest-' + name + '.json', { merge: true }) )
			.pipe( gulp.dest( dest ))
			.pipe( revDel({ dest: dest }))
			.pipe( gulp.dest( dest ));
		}

		nextBundle();
	}

	return { bundle: bundle };
}

function buildJs( production ) {
	builder( 'quiz-app', './src/app/app.js', production ).bundle( './dist/static', 'quiz-app.min.js' );
	builder( 'quiz-editor', './src/app/editor.js', production ).bundle( './dist/static', 'quiz-editor.min.js' );
}

function buildSass( name, path ) {
	sass( path )
	.pipe( buffer() )
	.pipe( rename( name + '.css' ))
	.pipe( rev() )
	.pipe( gulp.dest( 'dist/static' ))
	.pipe( rev.manifest( 'css-manifest-' + name + '.json', { merge: true }) )
	.pipe( gulp.dest( 'dist/static' ));
}
