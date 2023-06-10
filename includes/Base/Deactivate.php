<?php

/**
 *
 * @package disk-usage
 */

namespace includes\Base;

class Deactivate {

	public static function deactivate() {
		flush_rewrite_rules();
	}
}