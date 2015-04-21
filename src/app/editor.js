var React = require( "react" );
var QuizEditor = require( "quiz-editor" ).Quiz;
var _ = require( "lodash" );
var Joi = require( "joi" );

var labels = require("../plugin/config/labels");

var mediaSchema = Joi.object().keys({
	image: Joi.string().required(),
	title: Joi.string().required(),
	caption: Joi.string().required(),
	altText: Joi.string().required()
}).required();

var quizSchema ={
	questions: Joi.array().min(1).max(10).items(Joi.object().keys({
		prompt: Joi.string().required(),
		media: mediaSchema,
		answers: Joi.array().items(Joi.string().required()).required(),
	}).required()).required(),
	results: Joi.array().min(2).max(9).items(Joi.object().keys({
		title: Joi.string().required(),
		text: Joi.string().required(),
		media: mediaSchema
	}).required()).required(),
};

var defaultQuizData = {
	questions: [{
		prompt: "",
		answers: ["", ""],
		media: {
			image: null,
			title: "",
			caption: "",
			altText: ""
		}
	}],
	results: [{
		title: "",
		text: "",
		media: {
			image: null,
			title: "",
			caption: "",
			altText: ""
		}
	}, {
		title: "",
		text: "",
		media: {
			image: null,
			title: "",
			caption: "",
			altText: ""
		}
	}]
};

jQuery( function( $ ) {

	var $quizDataDump = $( "#quiz-data-dump" );
	var initialQuizData = JSON.parse( _.unescape( $quizDataDump.attr( "value" )));

	var QuizEditorApp = React.createClass({
		render: function() {

			var errorMessage;
			if( this.state.invalid ) {
				errorMessage = (
					<div className="error">
						<p>{ labels.errorMessage }</p>
					</div>
				);
			}

			return (
				<div>
					{ errorMessage }
					<QuizEditor
						quiz={ this.state.quiz }
						updateQuiz={ function( quiz ) {
							var result = Joi.validate( quiz, quizSchema );

							this.setState({
								invalid: !!result.error,
								quiz: quiz
							});

							$quizDataDump.attr( "value", JSON.stringify( quiz ));
						}.bind( this )} />
				</div>
			);
		},

		getInitialState: function() {
			return {
				invalid: false,
				quiz: this.props.initialQuizData
			};
		}
	});

	function changed( state ) {
		return !_.isEqual( initialQuizData, state );
	}

	function isValid( state ) {
		var result = Joi.validate( state, quizSchema ).error;
		return !!result.error;
	}

	var $form = $( "#post" );
	$form.submit( function( event ) {
		var state = JSON.parse( $quizDataDump.val() );

		console.log( changed( state ), !isValid( state ));
		console.log( initialQuizData, state);

		if( changed( state ) && !isValid( state )) {
			if( confirm( labels.confirmDropChanges )) {
				$quizDataDump.attr( "value", JSON.stringify( initialQuizData ));
			} else {
				event.preventDefault();
			}
		}
	});

	React.render( <QuizEditorApp initialQuizData={ _.cloneDeep( initialQuizData ) || defaultQuizData }/>, document.getElementById( "quiz-editor" ));
});
