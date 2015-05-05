# Quiz Adaptor

A Wordpress quiz plugin. This is a replacement for the "WP My Quiz" plugin, which can be found in old (Pre May 2015) versions of the kasra.co codebase. It integrates the [Kasra quiz app](https://bitbucket.org/menapost/quiz-user-front-end), the [Kasra quiz editor](https://bitbucket.org/menapost/quiz-editor), and the [Kasra WordPress site](https://bitbucket.org/menapost/kasra-wp).

If you find issues or have feature requests, please either file them in the relevant project's issue tracker or file them with the quiz adaptor issue tracker if you are unsure where to file it. If you do not have access to the issue tracker, please ping Dan in the Slack #dev room. If you do not have access to Slack (Are you even part of the Kasra project?) then email Dan: daniel.ross@kasra.co.

Issues and feature requests should be replicated into the sprint backlog, until Atlassian improves Bitbucket integration with Jira, or someone schools Dan on how to Atlassian.

## Usage

### Taking a Quiz

The quiz app allows the user to fill out the quiz once, and then displays a result. The result can be shared on Twitter and Facebook, which posts a story / tweet that links back to the article.

Once the last question has been answered, the quiz is complete, and responses cannot be changed. To retake the quiz, refresh the page.

### Writing a New Quiz Article

Start writing a quiz object in the "Quiz" meta box on the WordPress post editor. Any article that has quiz data is considered to be a quiz article. Quiz articles can be saved, published and scheduled for publishing by users with the appropriate permissions. Quiz articles may have a "draft" quiz and a "published" quiz.

When a quiz is being edited, a validation message is shown explaining the criteria required for a valid quiz:

- All fields filled in, including images
- At least two results
- At least one question

Each result and each question has an image associated with it. When a new image is selected, it is opened in a crop tool. The [crop tool](https://bitbucket.org/menapost/selectrect) outputs a cropped version of the selected image, which is saved in the image field of the media object as an optimized JPG, encoded as a base64 data URI.

### Publishing a Quiz Article

Articles having an invalid quiz can be saved and published. However, the invalid quiz is saved as a draft, and not shown on the front end. If the article had a valid quiz previously, then that will still be shown on the front end, until the draft quiz is valid.

When a valid quiz is saved, it is saved as the published quiz, and the draft quiz is removed.

When loading a quiz article in the editor, the draft quiz is loaded, if one exists. If not, then the published quiz is loaded, if one exists. When editing an article that has no quiz data, the quiz editor shows an empty, minimal quiz: two empty results and one empty question.

### Editing an Old Quiz Article

Quiz articles that were created using the old WP My Quiz plugin should be loaded in the quiz editor; they have been migrated to the new format on [kasra.staging.wpengine.com]() (Note: update this after final migration on kasra.co).

Validation rules are different for legacy quizzes. The same criteria that are applied to new quizzes are applied to legacy quizzes, and the same instructional error message is shown when an invalid quiz is being edited. However, when any legacy quiz article is being saved, the quiz is saved as a published version, not a draft. This is because legacy quizzes were not validated, and may be missing required information. Requiring an editor to fill all fields in order to make any change to a legacy quiz would either encourage editors to add junk data to pass validation, or discourage them from making incremental fixes to legacy quizes.

## Implementation

Quizzes are stored as a JSON blob in `wp_postmeta`.

```
{
	draft: <quiz object, optional>,
	published: <quiz object, optional>,
	legacy: boolean
}
```

See the [quiz service](https://bitbucket.org/menapost/quiz-service) for the quiz schema. Note that the quiz service is not currently in use.

### Editor

Quiz data is saved in a `save_post_post` [action](https://codex.wordpress.org/Plugin_API/Action_Reference/save_post). If a quiz object is present, then the article is a quiz article, and the quiz data is saved.

When saving a quiz object, image URLs are scanned for data URIs, which are base64 encoded JPG images. When these are found, they are saved as files under `wp-content/uploads/<year>/<month>`. The image URL is then replaced with the URL of the saved file.

### Front End App

When a quiz article is viewed, the quiz app loads the published version of the article's quiz, if one exists.

Sharing features are simply links to Facebook and Twitter endpoints, that contain quiz result info as GET parameters. 3rd party scripts, provided by those social networks, handle click actions on those links.
