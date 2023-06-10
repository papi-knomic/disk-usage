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
		$usage_stats = []; // Replace with your code to gather the usage stats

		// Return the gathered usage stats in the AJAX response
		wp_send_json($usage_stats);
	}
}