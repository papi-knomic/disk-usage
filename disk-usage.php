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

//Stops file from being called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

define('DISK_USAGE_VERSION', '1.0.0');
define('PLUGIN_OPTION_GROUP', 'wp_disk_usage_option_group');

// Require once for the composer autoload
if (file_exists(dirname( __FILE__ ) . '/vendor/autoload.php')) {
	require_once dirname( __FILE__) . '/vendor/autoload.php';
}


/*
 * Plugin activation
 */
function activatePlugin() {
	\includes\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activatePlugin');

/*
 * Plugin deactivation
 */
function deactivatePlugin() {
	\includes\Base\Deactivate::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivatePlugin');

if ( class_exists( 'includes\\init' ) ) {
	includes\Init::registerServices();
}