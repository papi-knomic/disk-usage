<?php
/**
 * @package wp-disk-usage
 */

namespace includes\Base;

/**
 * Class Admin
 * @package includes\Base
 */
class Admin extends BaseController
{
	/**
	 * Register admin-related functionality.
	 */
	public function register(): void
	{
		add_action('admin_menu', [$this, 'addMenuPage']);
		add_action('admin_menu', [$this, 'addSettingsPage']);
	}

	/**
	 * Add the main menu page.
	 */
	public function addMenuPage(): void
	{
		add_menu_page(
			'WP Disk Usage',
			'Disk Usage',
			'administrator',
			'wp-disk-usage',
			[$this, 'mainPageRender'],
			'dashicons-chart-area',
			110
		);
	}

	/**
	 * Render the main menu page.
	 */
	public function mainPageRender(): void
	{
		$usage_stats_exist = get_option('disk_usage_stats_exists');
		$usage_stats_exist = (bool)$usage_stats_exist ?? false;
		$files = ( new DiskUsage())->generateFileTree();

		require_once $this->plugin_path . 'templates/admin.php';
	}

	/**
	 * Add the settings submenu page.
	 */
	public function addSettingsPage(): void
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

	/**
	 * Render the settings submenu page.
	 */
	public function settingsPageRender(): void
	{
		require_once $this->plugin_path . 'templates/settings.php';
	}
}
