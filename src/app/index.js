let React = require( "react" );
let Quiz = require( "quiz" ).Quiz;

// Expects quizData to have been initialized by something like wp_localize_script
React.render( <Quiz quiz={ window.quizContent } title={ window.quizTitle }/>, document.getElementById( "quiz-mount-point" ));
