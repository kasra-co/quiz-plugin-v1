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
`GET /quiz/:quizId` | | `200 OK` Quiz object<br>`404 Not Found`
`POST /post/:postId/quiz` | Save a new quiz and associate it with the `:postId` post. | `201 Created`: Quiz object<br>`404 Not Found` if the post does not exist (quizzes must be associated with a post)
`PUT /quiz/:quizId` | Replace a quiz | `201 Created`: Quiz object
`POST /quiz/:quizId/question` | Add a question to a quiz | `201 Created`: Question object
`PUT /quiz/:quizId/question/:questionIndex` | Replace a question on a quiz, identified by its position in the quiz | `201 Created`: Question object
`PUT /quiz/:quizId/question/:questionIndex/image`, `content-type: multipart/form-data` | Set the header image for a question | `201 Created`: Image url

## Front End Facade

A WP data model is provided that acts as a facade to hide the extra requirements imposed by Wordpress integration. When using this module to handle all communication with the server, the client does not need to know whether it is talking to the WP adaptor or to the new article service.

The model's API is described in the quiz-editor documentation.

The extra WP related data needed by the facade implementation should be embedded in a global object in a script tag in the rendered page:

```javascript
wp_quiz_data = {
    post_id,
    quiz_id
}
```

## WP Post Editor Block

A replacement for the current quiz editor block in the WP post editor. Embeds the new quiz editor and provides the facade for communication with the adaptor, which communicates with the new article service.
