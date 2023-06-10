<?php
/**
 * @package alumunite-events
 */

namespace includes\Base;


class Admin extends BaseController
{

    public function register(): void
    {
        add_action( 'admin_menu', [$this, 'addAdminPages']);
	    add_action('admin_menu', [$this, 'addSettingsPage']);
    }

    public function addAdminPages(): void
    {
        add_menu_page('WP Disk Usage', 'Main Page', 'administrator', 'wp-disk-usage', [$this, 'adminIndex'],'dashicons-chart-area', 110 );
    }


    public function adminIndex(): void
    {
	    // Display the content of the main page
	    echo '<div class="wrap">';
	    echo '<h1>Disk Usage Plugin</h1>';
	    // Add your main page content here
	    echo '</div>';
//        require_once $this->plugin_path . 'templates/admin.php';
    }

	public function addSettingsPage():void
	{
		add_submenu_page(
			'wp-disk-usage',
			'Disk Usage Settings',
			'Settings',
			'administrator',
			'wp-disk-usage-settings',
			[$this, 'settingsPageRender']
		);
	}

	public function settingsPageRender(): void
	{
		echo '<div class="wrap">';
		echo '<h1>Disk Usage Plugin Settings</h1>';
		// Add your main page content here
		echo '</div>';
	}
}