<?php

// See http://ottopress.com/2009/wordpress-settings-api-tutorial/

add_action( 'admin_init', function() {
	register_setting( 'menapost-quiz-options', 'menapost-quiz-options', function( $input ) {
		$normalized[ 'menapost_quiz_endpoint' ] = trim( $input[ 'menapost_quiz_endpoint' ]);
		return $normalized;
	});

	add_settings_section( 'menapost-quiz-article-service', __( 'Quiz Service' ), 'renderArticleServiceSection', 'menapost-quiz' );

	add_settings_field( 'menapost_quiz_endpoint', __( 'Quiz service endpoint URL' ), 'renderEndpointURLControl', 'menapost-quiz', 'menapost-quiz-article-service' );
});

function renderArticleServiceSection() { ?>
	<p>Specify the URL of the quiz service. This will be used for saving and retrieving quizzes. Example: <code>http://localhost:3001</code>. This configuration should be obsoleted once we have a service discovery process.</p>
<?php }

function renderEndpointURLControl() {
	$options = get_option( 'menapost-quiz-options' );
	?><input name="menapost-quiz-options[menapost_quiz_endpoint]" id="quiz-endpoint" type="text" value="<?= $options[ 'menapost_quiz_endpoint' ] ?>"/><?php
}
