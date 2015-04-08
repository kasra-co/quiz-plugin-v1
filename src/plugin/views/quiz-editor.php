<?php

function renderQuizEditor( $quiz ) {
	$value = htmlspecialchars( json_encode( $quiz, true ), ENT_COMPAT, 'UTF-8' );
	var_dump( json_encode( $quiz) );

	?><div id="quiz-editor">
		<p>Quiz data in hidden element</p>
		<input type="hidden" id="quiz" name="quiz" value="<?= $value ?>"/>
	</div><?php
}
