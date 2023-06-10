<?php

/**
 *
 * @package disk-usage
 */

namespace includes\Base;

class Deactivate {

	public static function deactivate() {
		flush_rewrite_rules();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		global $wpdb;
		$table_name = $wpdb->prefix . 'disk_usage';
		$sql = "DROP TABLE IF EXISTS $table_name";

		$wpdb->query($sql);
	}
}