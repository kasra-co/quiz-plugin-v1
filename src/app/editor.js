var React = require( "react" );
var QuizEditor = require( "quiz-editor" ).Quiz;
var _ = require( "lodash" );

jQuery( function( $ ) {
	var quizDataDump = $( "#quiz-data-dump" );
	var initialQuizData = JSON.parse( _.unescape( quizDataDump.attr( "value" )));

	var quizApp = (
		<QuizEditor
			initialQuiz={ initialQuizData }
			updateQuiz={ function( newQuiz ) {
				quizDataDump.attr( "value", JSON.stringify( newQuiz ));
			}}
		/>
	);

	React.render( quizApp, document.getElementById( "quiz-editor" ));
});
