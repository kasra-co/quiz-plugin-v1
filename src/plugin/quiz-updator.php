<?php

require_once( __DIR__ . '/lib/save-quiz.php' );

function mediaSaver( $postId ) {
	return function( $object ) use ( $postId ) {
		$uri = $object->media->image;
		$url = $uri;

		if( preg_match( '#^data:(?!//)#', $uri )) {
			$phpUri = 'data://' . substr( $uri, 5 );
			$fileInfo = wp_upload_bits( 'uploadedBits.png', null, file_get_contents( $phpUri )); // TODO: Use image
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

	if( $_POST[ 'quiz' ] === 'null' ) {
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

	if( isset( $quiz->draft )) {
		$quiz->draft->results = array_map( mediaSaver( $postId ), $quiz->draft->results );
		$quiz->draft->questions = array_map( mediaSaver( $postId ), $quiz->draft->questions );
	}

	if( isset( $quiz->published )) {
		$quiz->published->results = array_map( mediaSaver( $postId ), $quiz->published->results );
		$quiz->published->questions = array_map( mediaSaver( $postId ), $quiz->published->questions );
	}

	saveQuiz( $post, $quiz );
}, 10, 3); // Tell WP that we are using the 3 arg form
