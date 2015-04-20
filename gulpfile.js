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
var rename = require( 'gulp-rename' );

gulp.task( 'watch', [ 'sass', 'font', 'config', 'php', 'images' ], function() {
	gulp.watch( 'node_modules/quiz/style/**/*.scss', [ 'sass' ]);
	gulp.watch( 'node_modules/quiz-editor/style/**/*.scss', [ 'sass' ]);
	gulp.watch( 'src/plugin/**/*.php', [ 'php' ]);
	gulp.watch( 'src/plugin/config/**/*', [ 'config' ]);

	buildApp( true );
	buildEditor( true );
});

gulp.task( 'build', [ 'sass', 'font', 'config', 'php', 'images' ], function() {
	buildApp();
	buildEditor();
});

gulp.task( 'sass', function() {
	buildSass( 'quiz-app.min', 'node_modules/quiz/style/index.scss' );
	setTimeout( function() {
		buildSass( 'quiz-editor.min', 'node_modules/quiz-editor/style/index.scss' );
	}, 2000 );
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

gulp.task( 'images', function() {
	gulp.src( 'node_modules/quiz-editor/images/**/*' )
	.pipe( gulp.dest( 'dist/images' ));
});

gulp.task( 'clean', function() {
	del([ 'dist' ]);
});

function buildApp( debug ) {
	var appBundler = watchify( browserify(
		'./src/app/app.js',
		_.extend(
			watchify.args,
			{ debug: debug }
		)
	));

	appBundler.transform( 'reactify' );
	appBundler.transform( 'es6ify' );

	appBundler.on( 'update', bundleApp );
	appBundler.on( 'log', gutil.log );

	function bundleApp() {
		return appBundler.bundle()
		.on( 'error', gutil.log.bind( gutil, 'Browserify error' ))
		.pipe( source( 'quiz-app.min.js' ))
		.pipe( gulp.dest( 'dist/static' ));
	}

	bundleApp();
}

function buildEditor( debug ) {
	var editorBundler = watchify( browserify(
		'./src/app/editor.js',
		_.extend(
			watchify.args,
			{ debug: debug }
		)
	));

	editorBundler.transform( 'reactify' );
	editorBundler.transform( 'es6ify' );

	editorBundler.on( 'update', bundleEditor );
	editorBundler.on( 'log', gutil.log );

	function bundleEditor() {
		return editorBundler.bundle()
		.on( 'error', gutil.log.bind( gutil, 'Browserify error' ))
		.pipe( source( 'quiz-editor.min.js' ))
		.pipe( gulp.dest( 'dist/static' ));
	}

	bundleEditor();
}

function buildSass( name, path ) {
	sass( path )
	.pipe( buffer() )
	.pipe( rename( name + '.css' ))
	.pipe( gulp.dest( 'dist/static' ));
}
