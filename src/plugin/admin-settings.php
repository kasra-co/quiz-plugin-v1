<?php

// See http://ottopress.com/2009/wordpress-settings-api-tutorial/

add_action( 'admin_init', function() {

	// Replace all plugin options
	register_setting( 'menapost-quiz-options', 'menapost-quiz-options', function( $input ) {
		$normalized[ 'quizEndpoint' ] = trim( $input[ 'quizEndpoint' ]);
		return $normalized;
	});

	add_settings_section( 'menapost-quiz-article-service', __( 'Quiz Service' ), 'renderArticleServiceSection', 'menapost-quiz' );

	add_settings_field( 'quizEndpoint', __( 'Quiz service endpoint URL' ), 'renderEndpointURLControl', 'menapost-quiz', 'menapost-quiz-article-service' );
});

function renderArticleServiceSection() { ?>
	<p>Specify the URL of the quiz service. This will be used for saving and retrieving quizzes. Example: <code>http://localhost:3001</code>. This configuration should be obsoleted once we have a service discovery process.</p>
<?php }

function renderEndpointURLControl() {
	$options = get_option( 'menapost-quiz-options' );

	if( isset( $options[ 'quizEndpoint' ])) {
		$value = $options[ 'quizEndpoint' ];
	} else {
		$value = '';
	}

	?><input name="menapost-quiz-options[quizEndpoint]" id="quiz-endpoint" type="text" value="<?= $value ?>"/><?php
}
