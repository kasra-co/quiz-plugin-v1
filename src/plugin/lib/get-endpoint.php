<?php

function getEndpoint() {
	// get service info
	$options = get_option( 'menapost-quiz-options', null );
	$endpoint = $options[ 'quizEndpoint' ];

	if( $endpoint === null ) {
		trigger_error( 'Menapost quiz: article endpoint undefined', E_USER_WARNING );
		return;
	}

	return $endpoint;
}
