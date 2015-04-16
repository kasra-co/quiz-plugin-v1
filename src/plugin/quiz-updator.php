<?php

require_once( __DIR__ . '/lib/save-quiz.php' );

function mediaSaver( $postId ) {
	return function( $object ) use ( $postId ) {
		$uri = $object->media->image;
		$url = $uri;

		if( preg_match( '#^data:(?!//)#', $uri )) {
			$phpUri = 'data://' . substr( $uri, 5 );
			$fileInfo = wp_upload_bits( 'uploadedBits.png', null, file_get_contents( $phpUri ));
			$url = $fileInfo[ 'url' ];
		}

		$object->media->image = $url;
		return $object;
	};
}

add_action( 'save_post_post', function( $postId, $post, $update ) {
	if( !isset( $_POST ) || !isset( $_POST[ 'quiz' ] )) {
		return;
	}

	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if( !current_user_can( 'edit_post', $postId )) {
		return;
	}

	$quiz = json_decode( htmlspecialchars_decode( wp_unslash( $_POST[ 'quiz' ]), ENT_COMPAT ));

	if( !isset( $quiz )) {
		trigger_error( 'Quiz: Bad JSON' );
		return;
	}

	// This is not a quiz article, it has no quiz
	if( $quiz === null ) {
		return;
	}

	if( !isset( $quiz->questions ) || !isset( $quiz->results )) {
		trigger_error( 'Quiz: Missing questions or results' );
		return;
	}

	$quiz->results = array_map( mediaSaver( $postId ), $quiz->results );
	$quiz->questions = array_map( mediaSaver( $postId ), $quiz->questions );

	saveQuiz( $post->post_name, $quiz );
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
