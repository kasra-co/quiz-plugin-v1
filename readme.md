# Quiz Adaptor

A Wordpress plugin for displaying quizes and embedding the quiz editor.

Quizzes are associated by their MongoDB ID with post ids using a foreign key table:

post_id                   | quiz_id
--------------------------|--------
int (primary key, unique) | varchar(32) (unique)

See the [quiz service](https://bitbucket.org/menapost/quiz-service) for the quiz schema.

## API

Most requests are simply piped to the appropriate route on the quiz service, after validating that the user has the correct permissions. All requests should send and expect `application/json`, unless specified otherwise.

Endpoint: `/wp-quiz`

Route | Description | Returns
--|--|--
`POST /quiz/:postId` | Save a new quiz and associate it with the `:postId` post. | `201 Created`: Quiz object, `404 Not Found` if the post does not exist (quizzes must be associated with a post)
`GET /quiz/:postId` | | `200 OK` Quiz object, `404 Not Found`
`PUT /quiz/:postId` | Replace a quiz | `201 Created`: Quiz object
`POST /quiz/result/:quizId/:userId` | Create a result post for a user | `201 Created`: post url

## Adaptor Front End

A WP data model is provided that acts as a facade to hide the extra requirements imposed by Wordpress integration. When using this module to handle all communication with the server, the client does not need to know whether it is talking to the WP adaptor or to the new article service.

The model's API is described in the quiz-editor documentation.

The extra WP related data needed by the facade implementation should be embedded in a global object in a script tag in the rendered page:

```php
<script>
	wpQuizData = {
		postId: '<?= $post.ID ?>';
	}
</script>
```

The quiz data can then be requested from the front end and used to render the quiz editor, in a page template provided by this adaptor. This adaptor will pass a `hostAdaptor` object to the QuizEditor component, which contains methods for communicating with the backend in a generic way. This saves the quiz module from having to know what backend it is talking to.

```javascript
request.get( '/quiz/' + window.WPQuizData.post_id )
.set( 'Accept', 'application/json' )
.end( function( err, res ) {
	React.render( <QuizEditor initialQuiz={ res.body.quiz }, hostAdaptor={ WPHostAdaptor }/>, document.getElementById( 'the-quiz-editor-box' ));
});

// Try to keep WP adaptor API specific logic out of the actual quiz editor module. The quiz editor should just export a QuizEditor component for us to render here.
var hostAdaptor = {
	saver: function( done ) {
		return function( quiz ) {
			var method = quiz._id? 'PUT', 'POST';

			request[ method ]( '/quiz/' + window.WPQuizData.post_id )
			.set( 'ContentType', 'application/json' )
			.end( done );
		}
	}
};
```

The quiz editor actions should use the hostAdaptor functions for server communication:

```javascript
var save = Reflux.createAction({
	asyncResult: true,
	preEmit: function( quiz ) {
		hostAdaptor.saver( function( error, result ) {
			if( error ) {
				return this.failed( error ); // A child action of any asyncResult action
			}

			this.completed( result.body );
		}.bind( this ))( quiz );
	}
});
```

## WP Post Editor Block

A replacement for the current quiz editor block in the WP post editor. Embeds the new quiz editor and provides the facade for communication with the adaptor, which communicates with the new article service.