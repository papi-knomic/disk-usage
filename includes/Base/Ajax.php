<?php

namespace includes\Base;

class Ajax extends BaseController
{
	public function register(): void
	{
		add_action('wp_ajax_gather_disk_usage_results', [ $this, 'gatherDiskUsageResults']);
		add_action('wp_ajax_nopriv_gather_disk_usage_results', [ $this, 'gatherDiskUsageResults']);
	}

	public function gatherDiskUsageResults ():void
	{
		var_dump('omo');
		die();
		$usage_stats_exist = get_option('disk_usage_stats_exists');
		if ( empty($usage_stats_exist) ) {
			update_option('disk_usage_stats_exists', false);
		}

		$usage_stats = [];

		wp_send_json($usage_stats);
	}
}