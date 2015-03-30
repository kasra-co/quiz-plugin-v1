<?php

$title = __( BRAND . ' Quiz Settings' );

$renderAdminSettingsControls = function () use ( $title ) { ?>
	<div>
		<h2><?= $title ?></h2>
		<form action="options.php" method="POST">
			<?php settings_fields( 'menapost-quiz-options' ); ?>
			<?php do_settings_sections( 'menapost-quiz' ); ?>

			<input name="Submit" type="submit" value="<?= esc_attr__( 'Save Changes' )?>"/>
		</form>
	</div>
<?php };

add_action( 'admin_menu', function() use ( $title, $renderAdminSettingsControls ) {
	add_options_page( $title, $title, 'manage_options', 'quiz-options', $renderAdminSettingsControls );
});

