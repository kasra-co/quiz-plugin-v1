var React = require( "react" );
var Quiz = require( "quiz" );

// Expects quizData to have been initialized by something like wp_localize_script
React.render( <Quiz quiz={ window.quizData }/>, document.getElementByID( "quiz" ));
