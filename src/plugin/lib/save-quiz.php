<?php

function saveQuiz( $post, $quiz ) {
	update_post_meta( $post->ID, 'quiz', wp_slash( json_encode( $quiz )));
}
