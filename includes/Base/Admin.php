<?php
/**
 * @package alumunite-events
 */

namespace includes\Base;


class Admin extends BaseController
{

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
	    add_action('admin_menu', [$this, 'addSettingsPage']);
    }

    public function addMenuPage(): void
    {
        add_menu_page('WP Disk Usage', 'Disk Usage', 'administrator', 'wp-disk-usage', [$this, 'mainPageRender'],'dashicons-chart-area', 110 );
    }


    public function mainPageRender(): void
    {
	    $usage_stats_exist = get_option('disk_usage_stats_exists');
		$usage_stats_exist = (bool)$usage_stats_exist ?? false;
        require_once $this->plugin_path . 'templates/admin.php';
    }

	public function addSettingsPage():void
	{
		add_submenu_page(
			'wp-disk-usage',
			'Disk Usage Settings',
			'Settings',
			'manage_options',
			'wp-disk-usage-settings',
			[$this, 'settingsPageRender']
		);
	}

	public function settingsPageRender(): void
	{
		if (isset($_POST['submit'])) {
			$worker_time = sanitize_text_field($_POST['disk_usage_worker_time']);
			update_option( 'disk_usage_worker_time', $worker_time);
			echo '<div class="notice notice-success is-dismissible"><p>Options saved.</p></div>';
		}

		$pluginOptionGroup = 'wp_disk_usage_option_group';
		require_once $this->plugin_path . 'templates/settings.php';
	}
}