<?php

require_once ( __DIR__ . '/get-endpoint.php' );

function httpPostObject( $url, $data ) {
	$request = curl_init();

	curl_setopt( $request, CURLOPT_URL, $url );
	curl_setopt( $request, CURLOPT_POST, true );
	curl_setopt( $request, CURLOPT_POSTFIELDS, json_encode( $data ));
	curl_setopt( $request, CURLOPT_HTTPHEADER, Array(
		'Content-Type: application/json',
		'Accept: application/json'
	));
	curl_setopt( $request, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );

	$response = curl_exec( $request );
	$status = curl_getinfo( $request, CURLINFO_HTTP_CODE );
	curl_close( $request );

	if( $status !== 200 ) {
		trigger_error( "Menapost quiz: Failed to save quiz, article service returned $status: $response", E_USER_WARNING );
		return;
	}

	$article = json_decode( $response );
	return $article->quiz;
};

require_once( __DIR__ . '/get-endpoint.php' );

function saveQuiz( $post, $quiz ) {

	$article = [
		'slug' => $post->post_name,
		'quiz' => $quiz
	];

	return httpPostObject( getEndpoint() . "/article/quiz-article", $article );
}
