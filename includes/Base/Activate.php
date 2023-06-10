<?php

/**
 *
 * @package disk-usage
 */

namespace includes\Base;

class Activate {

	public static function activate()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		global $wpdb;
		$table_name = $wpdb->prefix . 'disk_usage';
		$charset_collate = $wpdb->get_charset_collate();

		// Define the table structure
		$sql = "CREATE TABLE $table_name (
    id INT(11) NOT NULL AUTO_INCREMENT,
    state TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    PRIMARY KEY (id)
                   ) ENGINE=InnoDB $charset_collate;";

		// Create the table
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$wpdb->query($sql);

		flush_rewrite_rules();
	}
}