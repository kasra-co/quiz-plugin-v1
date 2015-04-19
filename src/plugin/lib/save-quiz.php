<?php

require_once( __DIR__ . '/get-endpoint.php' );

function saveQuiz( $post, $quiz ) {
	update_post_meta( $post->ID, 'quiz', json_encode( $quiz ));
}
