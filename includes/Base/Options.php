<?php

namespace includes\Base;

class Options {
	/**
	 * @return void
	 */
	public function register() : void
	{
		add_action('admin_init', [$this, 'registerSettings']);
	}

	/**
	 * @return void
	 */
	public function registerSettings() : void {
		$option_group = 'wp_disk_usage_option_group';
		$option_name  = 'disk_usage_worker_time';

		register_setting( $option_group, $option_name );

		$option = get_option( $option_name );
		if ( empty( $option ) ) {
			add_option( $option_name, 5 );
		}

		add_settings_section(
			'wp_disk_usage_options_section',
			'WP Disk Usage Settings',
			null,
			'wp-disk-usage-settings'
		);

		add_settings_field(
			'disk_usage_worker_time',
			'Worker Time(secs)',
			[ $this, 'workerTimeOption' ],
			'wp-disk-usage-settings',
			'wp_disk_usage_options_section'
		);
	}

		public function workerTimeOption(): void
	{
		$option_name = 'disk_usage_worker_time';
		$option_value = get_option( $option_name );
		echo '<input type="number" name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $option_value ) . '" required/>';
	}
}