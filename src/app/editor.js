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
	results: Joi.array().items(Joi.object().keys({
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

	var quizDataDump = $( "#quiz-data-dump" );
	var initialQuizData = JSON.parse( _.unescape( quizDataDump.attr( "value" )));

	var QuizEditorApp = React.createClass({
		render: function() {

			console.log( this.state );

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

							if( !result.error ) {
								quizDataDump.attr( "value", JSON.stringify( quiz ));
							}
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

	React.render( <QuizEditorApp initialQuizData={ initialQuizData || defaultQuizData }/>, document.getElementById( "quiz-editor" ));
});
