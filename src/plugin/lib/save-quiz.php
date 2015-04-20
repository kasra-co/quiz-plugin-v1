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
		trigger_error( "Menapost quiz: Failed to save new quiz, article service returned $status: $response", E_USER_WARNING );
		return;
	}

	$article = json_decode( $response );
	return $article->quiz;
};

function httpPutObject( $url, $data ) {
	$request = curl_init();

	curl_setopt( $request, CURLOPT_URL, $url );
	curl_setopt( $request, CURLOPT_CUSTOMREQUEST, 'PUT' );
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
		trigger_error( "Menapost quiz: Failed to save updated quiz, article service returned $status: $response", E_USER_WARNING );
		return;
	}

	$article = json_decode( $response );
	return $article->quiz;
};

function saveQuiz( $post, $quiz ) {

	$article = Array(
		'slug' => $post->post_name,
		'quiz' => $quiz
	);

	$slug = $post->post_name;
	$response = httpPutObject( getEndpoint() . "/article/$slug/quiz", $article );
	if( $response ) {
		return $response;
	}

	return httpPostObject( getEndpoint() . "/article/quiz-article", $article );
}
