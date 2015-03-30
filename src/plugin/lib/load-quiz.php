<?php

// Returns a post's quiz as an associative array
function loadQuiz( $postId ) {
	return json_decode( file_get_contents( __DIR__ . '/../config/demo-data.json' ), true );
}
