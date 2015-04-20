<?php

function loadQuiz( $post ) {
	$quiz =  json_decode( get_post_meta( $post->ID, 'quiz', true ));
	return $quiz;
}
