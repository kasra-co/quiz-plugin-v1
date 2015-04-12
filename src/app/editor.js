function fileEventHandler( handler ) {
	return function( event ) {
		handler( this.files[ 0 ]);
	};
}

function saveImage( file ) {
	var formData = new FormData();
	formData.append( "action", "quiz-add-image" );
	formData.append( "postId", window.postId );
	formData.append( "question", 0 );
	formData.append( "file", file );
	formData.append( "quizImageNonce", window.quizImageNonce );

	var request = new XMLHttpRequest();
	request.onreadystatechange = function() {
		if( request.readyState == 4 && request.status == 200 ) {
			console.log( request.responseText );
			var response = JSON.parse( request.responseText );
			window.quizEditorNonce = response.nextNonce;
		}
	};

	request.open( "POST", window.ajaxurl, true );
	request.send( formData );
}

document.getElementById( "quiz-image" ).addEventListener( "change", fileEventHandler( saveImage ), false );
console.log( window.postId );
