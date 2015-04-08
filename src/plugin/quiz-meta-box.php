<?php

require_once( __DIR__ . '/lib/load-quiz.php' );
require_once( __DIR__ . '/views/quiz-editor.php' );

add_action( 'add_meta_boxes', function() {

	$render = function () {
		global $post;
		renderQuizEditor( loadQuiz( $post->ID ));
	};

	add_meta_box( 'quizMeta', __( 'Quiz' ), $render, 'post' );
});
