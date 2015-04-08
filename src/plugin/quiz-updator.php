<?php

require_once( __DIR__ . '/lib/save-quiz.php' );

add_action( 'save_post_post', function( $post_id, $post, $update ) {
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if( !current_user_can( 'edit_post', $post_id )) {
		return;
	}

	if( !isset( $_POST ) ) {
		return;
	}

	$quiz = json_decode( htmlspecialchars_decode( wp_unslash( $_POST[ 'quiz' ]), ENT_COMPAT ));

	if( !$quiz ) {
		trigger_error( 'Quiz: Bad JSON' );
		return;
	}

	saveQuiz( $post->post_name, $quiz );
}, 10, 3); // Tell WP that we are using the 3 arg form
