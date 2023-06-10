<?php

/**
 *
 * @package disk-usage
 */

namespace includes\Base;

class Activate {

	public static function activate() {

		flush_rewrite_rules();
	}
}