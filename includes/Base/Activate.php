<?php

/**
 * @package wp-disk-usage
 */

namespace includes\Base;

/**
 * Class Activate
 * @package includes\Base
 */
class Activate
{
	/**
	 * Activate the plugin.
	 */
	public static function activate()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . FILE_DATA_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql1 = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            file_name text NOT NULL,
            file_path text NOT NULL,
            parent_path text NOT NULL,
            size bigint(20) NOT NULL,
            file_count bigint(20) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		$table_name = $wpdb->prefix . JOB_STATE_TABLE;

		$sql2 = "CREATE TABLE $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            current_file INT NOT NULL,
            total_files INT NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql1);
		dbDelta($sql2);
	}
}
