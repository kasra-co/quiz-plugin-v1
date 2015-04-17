<?php

require_once( __DIR__ . '/get-endpoint.php' );

function saveQuiz( $postSlug, $quiz ) {
	$httpPostObject = function( $url, $data ) {
		$http = [
			'http' => [
				'method' => 'POST',
				'header' => [ 'Content-Type: application/json' ],
				'content' => wp_unslash( json_encode( $data ))
			]
		];

		$context = stream_context_create( $http );

		$response = @file_get_contents( $url, false, $context );

		if( !$response ) {
			trigger_error( 'Menapost quiz: Failed to save quiz', E_USER_WARNING );
		}
	};

	$article = [
		'slug' => $postSlug,
		'quiz' => $quiz
	];

	$httpPostObject( getEndpoint() . "/article/quiz-article", $article );
}
