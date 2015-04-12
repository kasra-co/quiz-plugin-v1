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

	saveQuiz( $post->ID, $quiz );
}, 10, 3); // Tell WP that we are using the 3 arg form

add_action( 'wp_ajax_quiz-add-image', function() {
	/* FIXME: Always invalid
	if( !check_ajax_referer( -1, 'quizImageNonce', false )) {
		http_response_code( 403 );
		die( json_encode([ 'error' => 'invalidNonce' ]));
	}
	*/

	if( !current_user_can( 'edit_post', $_POST[ 'postId' ])) {
		http_response_code( 403 );
		die( json_encode([ 'error' => 'notAllowed' ]));
	}

	$theWordFileInAVariableThatwp_handle_uploadCanReferToByReference = 'file';
	$attachmentId = wp_handle_upload( $theWordFileInAVariableThatwp_handle_uploadCanReferToByReference, $_POST[ 'postId' ]);

	if( is_wp_error( $attachmentId )) {
		http_response_code( 500 );
		die;
	}

	$imageURL = wp_get_attachment_url( $attachmentId );

	$quiz = loadQuiz( $_POST[ 'postId' ]);
	$post = get_post( $_POST[ 'postId' ]);

	if( !$quiz ) {
		$quiz = [ 'slug' => $post->slug ];
	}

	$quiz[ 'questions' ][ $_POST[ 'question' ]][ 'media' ][ 'image' ] = $imageURL;
	saveQuiz( $post->ID, $quiz );

	die;
});
