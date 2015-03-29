<?php

function loadQuiz( $postId ) {
	return json_decode( file_get_contents( __DIR__ . '../config/demo-data.json' ));
}
