<div class="wrap">
	<form method="post" action="">
		<?php settings_fields( $pluginOptionGroup ); ?>
		<?php do_settings_sections('wp-disk-usage-settings'); ?>
		<?php submit_button(); ?>
	</form>
</div>
