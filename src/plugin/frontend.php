<?php

require_once( __DIR__ . '/lib/load-quiz.php' );

// If this post has a quiz, then append a div to the post. The quiz will be mounted within it.
add_filter( 'the_content', function( $content ) {
	global $post;

	if( !is_singular( 'post' )) {
		return $content;
	}

	$quiz = loadQuiz( $post );

	// If we don't have a quiz, or we only have a draft quiz, then don't try to load a quiz
	if( $quiz === null || ( !isset( $quiz->public ) && isset( $quiz->draft ))) {
		return $content;
	}

	wp_localize_script( 'quiz-frontend', 'quizContent', isset( $quiz->public )? $quiz->public: $quiz );
	wp_localize_script( 'quiz-frontend', 'quizTitle', $post->post_title );
	wp_localize_script( 'quiz-frontend', 'shortUrl', wp_get_shortlink( $post->ID ));
	wp_localize_script( 'quiz-frontend', 'siteUrl', site_url() );

	return $content . '<div id="quiz-mount-point"></div>';
});

add_action( 'wp_enqueue_scripts', function() {
	// TODO: check if post is a quiz post before enqueueing quiz scripts

	$staticRoute = plugin_dir_url( __DIR__ ) . plugin_basename( __DIR__ );

	wp_enqueue_script( 'quiz-frontend', $staticRoute . '/static/quiz-app.min.js', Array(), null, true );
	wp_enqueue_style( 'quiz-frontend', $staticRoute  . '/static/quiz-app.min.css' );
});
