<?php

/**
 * Plugin Name: WP Disk Usage
 * Plugin URI: https://github.com/papi-knomic/disk-usage
 * Description: The Disk Usage Plugin is a WordPress plugin that provides users with information about the disk usage of their website
 * Version: 1.0
 * Author: Samson Moses
 * Author URI: https://github.com/papi-knomic
 * Text Domain: wp-disk-usage
 */

// Stops file from being called directly
if (!defined('WPINC')) {
	die;
}

// Define constants
define('DISK_USAGE_VERSION', '1.0.0');
define('PLUGIN_OPTION_GROUP', 'wp_disk_usage_option_group');
define('PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JOB_STATE_TABLE', 'disk_usage_job_state');
define('FILE_DATA_TABLE', 'disk_usage_file_data');

// Require the composer autoload file
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Require the plugin functions file
require_once(PLUGIN_DIR . 'includes/functions.php');

// Activate plugin
function activatePlugin()
{
	\includes\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activatePlugin');

// Deactivate plugin
function deactivatePlugin()
{
	\includes\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivatePlugin');

// Register services on plugin initialization
if (class_exists('includes\\Init')) {
	includes\Init::registerServices();
}
