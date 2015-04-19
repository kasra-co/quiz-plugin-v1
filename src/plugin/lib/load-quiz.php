<?php

// Returns a post's quiz as an associative array
function loadQuiz( $post ) {
	$quiz =  json_decode( get_post_meta( $post->ID, 'quiz', true ));
	return $quiz;
}
