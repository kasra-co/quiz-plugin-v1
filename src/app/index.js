let React = require( "react" );
var Quiz = require( "quiz" );

// Expects quizData to have been initialized by something like wp_localize_script
React.render( <Quiz quiz={ window.quizContent } title={ window.quizTitle }/>, document.getElementById( "quiz-mount-point" ));
