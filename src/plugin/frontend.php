<?php

require_once( __DIR__ . '/lib/load-quiz.php' );

// If this post has a quiz, then append a div to the post. The quiz will be mounted within it.
add_filter( 'the_content', function( $content ) {
	global $post;

	if( !is_singular( 'post' )) {
		return $content;
	}

	$quiz = loadQuiz( $post->post_name );

	if( $quiz === null ) {
		return $content;
	}

	wp_localize_script( 'quiz-frontend', 'quizContent', $quiz);
	wp_localize_script( 'quiz-frontend', 'quizTitle', $post->post_title);

	return $content . '<div id="quiz-mount-point"></div>';
});

add_action( 'wp_enqueue_scripts', function() {
	// TODO: check if post is a quiz post before enqueueing quiz scripts

	$staticRoute = plugin_dir_url( __DIR__ ) . plugin_basename( __DIR__ );

	$jsRevs = json_decode( file_get_contents( $staticRoute . '/static/js-manifest-quiz-app.json' ), true );
	wp_enqueue_script( 'quiz-frontend', $staticRoute . '/static/' . $jsRevs[ 'quiz-app.min.js' ], [], null, true );

	$cssRevs = json_decode( file_get_contents( $staticPath . '/static/css-manifest-quiz-app.min.json' ), true );
	wp_enqueue_style( 'quiz-frontend', $staticRoute  . '/static/' . $cssRevs[ 'quiz-app.min.css' ]);
});
