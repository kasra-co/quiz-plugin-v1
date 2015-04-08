let React = require( "react" );
let Quiz = require( "quiz" );

// Expects quizData to have been initialized by something like wp_localize_script
React.render(
<Quiz
	quiz={ window.quizContent }
	quizTitle={ window.quizTitle }
/>,
document.getElementById( "quiz-mount-point" ));
