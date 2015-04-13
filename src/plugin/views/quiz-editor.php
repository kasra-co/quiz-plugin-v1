<?php

function renderQuizEditor( $quiz ) {
	$value = htmlspecialchars( json_encode( $quiz, true ), ENT_COMPAT, 'UTF-8' );
	?><div id="quiz-editor"></div><?php
}
