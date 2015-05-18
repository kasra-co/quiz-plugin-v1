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
} else {
	require_once( __DIR__ . '/frontend.php' );
}
