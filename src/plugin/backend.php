<?php

require_once( __DIR__ . '/quiz-meta-box.php' );
require_once( __DIR__ . '/quiz-updator.php' );

add_action( 'admin_enqueue_scripts', function( $hook ) {
	global $post;

	$staticPath = plugin_dir_url( __DIR__ ) . plugin_basename( __DIR__ );

	$jsRevs = json_decode( file_get_contents( $staticPath . '/static/rev-manifest-editor.json' ), true );
	wp_enqueue_script( 'quiz-editor', $staticPath . '/static/' . $jsRevs[ 'quiz-editor.min.js' ], [], null, true );

	if( isset( $post )) {
		wp_localize_script( 'quiz-editor', 'postId', $post->ID );
	}

	wp_localize_script( 'quiz-editor', 'quizEditorNonce', wp_create_nonce( 'quizEditorNonce '));
	wp_localize_script( 'quiz-editor', 'quizImageNonce', wp_create_nonce( 'quizImageNonce '));
});
