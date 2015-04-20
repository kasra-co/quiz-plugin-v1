<?php

require_once ( __DIR__ . '/get-endpoint.php' );

function httpGetObject( $url ) {
	$request = curl_init();

	curl_setopt( $request, CURLOPT_URL, $url );
	curl_setopt( $request, CURLOPT_HTTPHEADER, Array(
		'Accept: application/json'
	));
	curl_setopt( $request, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );

	$response = curl_exec( $request );
	$status = curl_getinfo( $request, CURLINFO_HTTP_CODE );
	curl_close( $request );

	if( $status !== 200 ) {
		trigger_error( "Menapost quiz: Failed to load quiz, article service returned $status: $response", E_USER_WARNING );
		return;
	}

	$article = json_decode( $response );
	return $article;
};

function fetchArticleQuiz( $endpoint, $postSlug ) {
	$article = httpGetObject( rtrim( $endpoint, '/' ) . '/article/' . $postSlug . '/quiz');

	if( isset( $article ) && isset( $article->quiz )) {
		return $article->quiz;
	}
};

// Returns a post's quiz as an associative array
function loadQuiz( $post ) {

	$endpoint = getEndpoint();

	return fetchArticleQuiz( $endpoint, $post->post_name );
}
