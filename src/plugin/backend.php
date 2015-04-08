<?php

require_once( __DIR__ . '/quiz-meta-box.php' );
require_once( __DIR__ . '/quiz-updator.php' );

add_action( 'admin_enqueue_scripts', function( $hook ) {
	global $post;

	// Only load the quiz editor on the post edit page
	wp_enqueue_script( 'quiz-editor', plugin_dir_url( __DIR__ ) . plugin_basename( __DIR__ . '/static/quiz-editor.min.js', [], VERSION, true ));
	wp_enqueue_style( 'quiz-editor', plugin_dir_url( __DIR__ ) . plugin_basename( __DIR__ . '/static/quiz-editor.css' ), [], VERSION );
});
