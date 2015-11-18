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
	draft: {
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
	}
};

jQuery( function( $ ) {

	var $quizDataDump = $( "#quiz-data-dump" );
	var initialQuizData = JSON.parse( _.unescape( $quizDataDump.attr( "value" )));

	// If initialQuizData is a quiz, not a container of a draft / published quiz, then it was created before we introduced draft / published quizes. Assume that it is valid and load it as a published quiz.
	if( initialQuizData && !initialQuizData.published && !initialQuizData.draft ) {
		initialQuizData = {
			published: initialQuizData
		};
	}

	var QuizEditorApp = React.createClass({
		render: function() {
			var errorMessage;

			var renderPublishedQuizControls = function() {
				return <button onClick={ this.loadPublic } type="button">{ labels.reset }</button>;
			}.bind( this );

			var renderUnpublishedQuizControls = function() {
				return <button onClick={ this.clear } type="button">{ labels.cancel }</button>;
			}.bind( this );

			if( this.state.invalid ) {
				errorMessage = (
					<div className="error">
						<p>{ labels.errorMessage }</p>
						{ this.props.initialQuizData.published? renderPublishedQuizControls(): renderUnpublishedQuizControls() }
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

							var articleQuiz;
							if( result.error && !this.props.initialQuizData.legacy ) {
								articleQuiz = {
									"draft": quiz,
								};

								if( initialQuizData && this.props.initialQuizData.published ) {
									articleQuiz.published = this.props.initialQuizData.published;
								}
							} else {
								articleQuiz = {
									"published": quiz,
								};
							}

							if( this.props.initialQuizData.legacy ) {
								articleQuiz.legacy = true;
							}

							$quizDataDump.attr( "value", JSON.stringify( articleQuiz ));
						}.bind( this )} />
						<div>
							<button type="button" onClick={() => {

								$quizDataDump.attr( "value", JSON.stringify({
									draft: _.cloneDeep(this.state.quiz),
									published: _.cloneDeep(this.state.quiz)
								}));
							}}>Force the quiz to update on the front end</button>
							<p>because dev is too busy with v2 to to troubleshoot the real problem, or to keep updating quizzes manually</p>
							<p>You will still need to hit "update" or "publish" for your changes to take effect.</p>
						</div>
				</div>
			);
		},

		getInitialState: function() {
			return {
				invalid: false,
				quiz: this.props.initialQuizData.draft || this.props.initialQuizData.published
			};
		},

		loadPublic: function() {
			this.replaceState({
				invalid: false,
				quiz: _.cloneDeep( initialQuizData.published )
			});
		},

		clear: function() {
			this.replaceState({
				invalid: false,
				quiz: _.cloneDeep( defaultQuizData.draft )
			});
		}
	});

	function changed( state ) {
		return !(
			( initialQuizData && initialQuizData.published && !_.isEqual( initialQuizData.published, state )) ||
			!_.isEqual( defaultQuizData, state )
		);
	}

	function isValid( state ) {
		var result = Joi.validate( state, quizSchema ).error;
		return !!result.error;
	}

	var $form = $( "#post" );
	$form.submit( function( event ) {
		var state = JSON.parse( $quizDataDump.val() );

		if( changed( state )) {
			$quizDataDump.attr( "value", JSON.stringify( initialQuizData ));
		}
	});

	React.render( <QuizEditorApp initialQuizData={ _.cloneDeep( initialQuizData || defaultQuizData )}/>, document.getElementById( "quiz-editor" ));
});
