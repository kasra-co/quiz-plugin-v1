<?php

function renderQuizEditor( $quiz ) {
?>
	<div id="quiz-editor"></div>
	<input type="hidden" name="quiz" value="<?= htmlspecialchars( json_encode( $quiz )) ?>" id="quiz-data-dump"/>
<?php
}
