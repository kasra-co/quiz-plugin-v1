<?php
/*
Plugin Name: Square Peg Quiz Adaptor
Description: A simple module for making simple quizzes.
Version: 0.1.0
Author: Dan Ross, menaPOST
*/

define( 'VERSION', '0.1.0' );
define( 'BRAND', 'menaPOST' ); // I keep forgetting the proper capitalization

if( is_admin() ) {
	require_once( __DIR__ . '/backend.php' );
} else {
	require_once( __DIR__ . '/frontend.php' );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	ob_start(); ?>
		<a href="<?= admin_url( 'options-general.php?page=quiz-options' ) ?>">Settings</a>
	<?php $links[] = ob_get_clean();
	return $links;
});
