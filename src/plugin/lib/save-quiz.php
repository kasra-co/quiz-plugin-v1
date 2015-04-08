<?php

require_once( __DIR__ . '/get-endpoint.php' );

function saveQuiz( $slug, $quiz ) {
	function httpPostObject( $url, $data ) {
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => [ 'Content-Type: application/json' ],
				'content' => json_encode( $data )
			]
		]);

		$response = file_get_contents( $url, false, $context );

		if( !$response ) {
			trigger_error( 'Menapost quiz: Failed to save quiz', E_USER_WARNING );
		}
	}

	$article = [
		'slug' => $slug,
		'quiz' => $quiz
	];

	httpPostObject( getEndpoint() . "/article/quiz-article", $article );
}
