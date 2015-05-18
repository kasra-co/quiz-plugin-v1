<?php
/*
Plugin Name: Square Peg Quiz Adaptor
Description: A simple module for making simple quizzes.
Version: 0.1.0
Author: Dan Ross, menaPOST
*/

define( 'QUIZ_ADAPTOR_BRAND', 'Kasra' );

if( is_admin() ) {
	require_once( __DIR__ . '/backend.php' );
	require_once( __DIR__ . '/admin-settings.php' );
	require_once( __DIR__ . '/admin-settings-page.php' );
} else {
	require_once( __DIR__ . '/frontend.php' );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	ob_start(); ?>
		<a href="<?= admin_url( 'options-general.php?page=quiz-options' ) ?>">Settings</a>
	<?php $links[] = ob_get_clean();
	return $links;
});
