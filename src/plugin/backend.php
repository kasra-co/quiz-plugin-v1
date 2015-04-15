<?php

require_once( __DIR__ . '/quiz-meta-box.php' );
require_once( __DIR__ . '/quiz-updator.php' );

add_action( 'admin_enqueue_scripts', function( $hook ) {
	global $post;

	$staticRoute = plugin_dir_url( __DIR__ ) . plugin_basename( __DIR__ );

	wp_enqueue_script( 'quiz-editor', $staticRoute . '/static/quiz-editor.min.js', [], null, true );
	wp_enqueue_style( 'quiz-editor', $staticRoute  . '/static/quiz-editor.min.css' );

	if( isset( $post )) {
		wp_localize_script( 'quiz-editor', 'postid', $post->id );
	}

	wp_localize_script( 'quiz-editor', 'quizEditorNonce', wp_create_nonce( 'quizEditorNonce '));
	wp_localize_script( 'quiz-editor', 'quizImageNonce', wp_create_nonce( 'quizImageNonce '));
});
