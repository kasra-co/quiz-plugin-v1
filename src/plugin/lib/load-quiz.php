<?php

require_once( __DIR__ . '/get-endpoint.php' );

// Returns a post's quiz as an associative array
function loadQuiz( $postSlug ) {

	function fetchArticleQuiz( $endpoint, $postSlug ) {
		$article = httpGetObject( rtrim( $endpoint, '/' ) . '/article/' . $postSlug );

		if( !$article ) {
			return null;
		}

		return $article->quiz;
	}

	function httpGetObject( $url ) {
		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'header' => [
					'Accept: application/json',
					'Accept-Charset: utf-8'
				]
			]
		]);

		$response = file_get_contents( $url, false, $context );

		$matches = [];
		preg_match( '/^\S+ (\d+)/', $http_response_header[ 0 ], $matches );
		$status = (int) $matches[ 0 ];
		if( $status !== 200 ) {
			return null;
		}

		return json_decode( $response );
	}

	$endpoint = getEndpoint();

	$article = fetchArticleQuiz( $endpoint, $postSlug );

	if( !$article ) {
		return null;
	}

	return $article->quiz;
}
