<?php

defined( 'ABSPATH' ) || exit;

if( !current_user_can( 'administrator' ) ) {
	wp_die(
		__( "We're sorry, you're not allowed to access Disk Usage." )
	);
}
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <form method="post" action="options.php">
		<?php settings_fields( PLUGIN_OPTION_GROUP ); ?>
		<?php do_settings_sections( 'wp-disk-usage-settings' ); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( 'Worker Time (secs)', 'your-plugin-domain' ); ?></th>
                <td>
					<?php
					$option_name = 'disk_usage_worker_time';
					$option_value = get_option( $option_name );
					?>
                    <input type="number" name="<?php echo esc_attr( $option_name ); ?>" value="<?php echo esc_attr( $option_value ); ?>" min="1" step="1" />
                    <p class="description"><?php esc_html_e( 'Defines how long a worker thread should run before saving state and stopping.', 'your-plugin-domain' ); ?></p>
                </td>
            </tr>
        </table>

		<?php submit_button(); ?>
    </form>
</div>
