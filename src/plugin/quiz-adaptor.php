<?php
/*
Plugin Name: Square Peg Quiz Adaptor
Description: A simple module for making simple quizzes.
Version: 0.1.0
Author: Dan Ross, menaPOST
*/

define( 'VERSION', '0.1.0' );

if( is_admin() ) {
} else {
	require_once( __DIR__ . '/frontend.php' );
}
