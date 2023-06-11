<?php

/**
 *
 * @package disk-usage
 */

namespace includes\Base;

class Deactivate {

	public static function deactivate() {
		global $wpdb;
		$table_name = $wpdb->prefix . FILE_DATA_TABLE;
		$sql1 = "DROP TABLE IF EXISTS $table_name";
		$table_name = $wpdb->prefix . JOB_STATE_TABLE;
		$sql2 = "DROP TABLE IF EXISTS $table_name";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql1);
		dbDelta($sql2);
	}
}