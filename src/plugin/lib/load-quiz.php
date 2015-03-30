<?php

// Returns a post's quiz as an associative array
function loadQuiz( $postId ) {

	function fetchArticleQuiz( $endpoint, $postId ) {
		$articleSlug = getQuizArticleSlugForPost( $postId );
		$article = httpGetObject( rtrim( $endpoint, '/' ) . '/article/' . $articleSlug );
		return $article->quiz;
	}

	function getQuizArticleSlugForPost( $postId ) {
		return get_post_meta( $postId, 'quiz-article-slug', true );
	}

	function httpGetObject( $url ) {
		$curler = curl_init( $url );
		curl_setopt( $curler, CURLOPT_HEADER, 0 );
		curl_setopt( $curler, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Accept-Charset: utf-8'
		]);
		curl_setopt( $curler, CURLOPT_RETURNTRANSFER, 1 );
		$response = curl_exec( $curler );
		curl_close( $curler );

		return json_decode( $response );
	}

	// get service info
	$options = get_option( 'menapost-quiz-options', null );
	$endpoint = $options[ 'quizEndpoint' ];

	if( $endpoint === null ) {
		trigger_error( 'Menapost quiz: article endpoint undefined', E_USER_WARNING );
		return;
	}

	//return fetchArticleQuiz( $endpoint, $postId );
	return json_decode( file_get_contents( __DIR__ . '/../config/demo-data.json' ), true );
}
