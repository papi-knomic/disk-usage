<?php

namespace includes\Base;

class Enqueue extends BaseController
{
	/**
	 * Register and enqueue admin scripts
	 *
	 * @return void
	 */
	public function register(): void
	{
		add_action('admin_enqueue_scripts', [$this, 'enqueue']);
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function enqueue(): void
	{
		if (is_admin()) {
			wp_enqueue_style('disk_usage_index_css', $this->plugin_url . 'assets/css/index.css', [], '1.0.0');
			wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css', [], '5.1.0' );
			wp_enqueue_style( 'bootstrap-icons-css', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css', array(), '1.5.0' );
			wp_enqueue_script('disk_usage_index', $this->plugin_url . 'assets/js/index.js', [], '1.0.0', true);
			wp_enqueue_script('disk_usage_controls', $this->plugin_url . 'assets/js/controls.js', ['jquery'], '1.0.0', true);
		}
	}
}