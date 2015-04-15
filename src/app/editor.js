var React = require( "react" );
var QuizEditor = require( "quiz-editor" ).Quiz;
var quiz;

var quizApp = (
	<QuizEditor
		updateQuiz={ function( newQuiz ) {
			quiz = newQuiz;
		}}
	/>
);

React.render( quizApp, document.getElementById( "quiz-editor" ));

document.getElementById( "post" ).addEventListener( "submit", function( event ) {
	$( event.target ).append( "<input type=\"hidden\" name=\"quiz\" value=\"" + JSON.stringify( quiz ) + "\"/>" );
});

