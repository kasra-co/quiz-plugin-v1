'use strict';

var gulp = require( 'gulp' );
var gutil = require( 'gulp-util' );
var watchify = require( 'watchify' );
var browserify = require( 'browserify' );
var source = require( 'vinyl-source-stream' );
var buffer = require( 'vinyl-buffer' );
var sass = require( 'gulp-sass' );
var _ = require( 'lodash' );
var del = require( 'del' );
var rev = require( 'gulp-rev' );
var revDel = require( 'rev-del' );

gulp.task( 'watch', [ 'sass', 'config', 'php' ], function() {
	gulp.watch( 'node_modules/quiz/style/**/*.scss', [ 'sass' ]);
	gulp.watch( 'src/plugin/**/*.php', [ 'php' ]);
	gulp.watch( 'src/plugin/config/**/*', [ 'config' ]);

	builder( './src/app/app.js', 'rev-manifest-app.json', true ).bundle( './dist/static', 'quiz-app.min.js' );
	builder( './src/app/editor.js', 'rev-manifest-editor.json', true ).bundle( './dist/static', 'quiz-editor.min.js' );
});

gulp.task( 'build', [ 'sass', 'config', 'php' ], function() {
	builder( './src/app/app.js', 'rev-manifest-editor.json', false ).bundle( './dist/static', 'quiz-app.min.js' );
	builder( './src/app/editor.js', 'rev-manifest-editor.json', false ).bundle( './dist/static', 'quiz-app.min.js' );
});

gulp.task( 'sass', function() {
	gulp.src( 'node_modules/quiz/style/index.scss' )
	.pipe( sass() )
	.pipe( buffer() )
	.pipe( rev() )
	.pipe( gulp.dest( 'dist/static' ))
	.pipe( rev.manifest( 'rev-manifest-css.json', { merge: true }) )
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

function builder( entry, manifest, isDev ) {
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
			.pipe( rev.manifest( manifest, { merge: true }) )
			.pipe( gulp.dest( dest ))
			.pipe( revDel({ dest: dest }))
			.pipe( gulp.dest( dest ));
		}

		nextBundle();
	}

	return { bundle: bundle };
}
