<?php

/**
 * @package wp-disk-usage
 */

namespace includes\Base;

/**
 * Class BaseController
 * @package includes\Base
 */
class BaseController
{
	/**
	 * @var string Plugin directory path
	 */
	public string $plugin_path;

	/**
	 * @var string Plugin directory URL
	 */
	public string $plugin_url;

	/**
	 * @var string Plugin file
	 */
	public string $plugin;

	/**
	 * BaseController constructor.
	 */
	public function __construct()
	{
		$this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
		$this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
		$this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/disk-usageSettings.php.php';
	}
}
