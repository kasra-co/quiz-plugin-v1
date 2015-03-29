<?php

require_once( __DIR__ . 'lib/load-quiz.php' );

wp_enqueue_script( 'quiz-frontend', plugin_dir_url( __DIR__ ) . 'static/index.js', [], VERSION, true );
wp_enqueue_style( 'quiz-frontend', plugin_dir_url( __DIR__ ) . 'static/style.css', [], VERSION, true );

// If this post has a quiz, then append a div to the post. The quiz will be mounted within it.
add_filter( 'the_content', function( $content ) {
	global $post;

	if( !is_singular( 'post' )) {
		return $content;
	}

	$quiz = loadQuiz( $post->ID );

	if( $quiz === null ) {
		return $content;
	}

	wp_localize_script( 'quiz-data', 'quizData', $quiz );

	return $content . '<div id="quiz"></div>';
});
