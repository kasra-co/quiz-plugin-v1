<?php

require_once( __DIR__ . '/get-endpoint.php' );

// Get a quiz for a post by it's slug, or undefined if one is not found
function fetchArticleQuiz( $endpoint, $postSlug ) {
	$article = httpGetObject( rtrim( $endpoint, '/' ) . '/article/' . $postSlug );

	if( isset( $article ) && isset( $article->quiz )) {
		return $article->quiz;
	}
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

	$response = @file_get_contents( $url, false, $context );

	$matches = [];
	// $http_response_header is a magic PHP var, dumped into our scope when file_get_contents is called with a url
	preg_match( '/^\S+ (\d+)/', $http_response_header[ 0 ], $matches );
	$status = (int) $matches[ 0 ];

	if( $status >= 500 ) {
		trigger_error( "Server error in article service:\n" . $response );
	} elseif( $response === false ) {
		return;
	}

	return json_decode( $response );
}

// Returns a post's quiz as an associative array
function loadQuiz( $postSlug ) {

	$endpoint = getEndpoint();

	$article = fetchArticleQuiz( $endpoint, $postSlug );

	if( !$article ) {
		return;
	}

	return $article->quiz;
}
