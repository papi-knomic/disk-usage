<?php
defined( 'ABSPATH' ) || exit;

if( !current_user_can( 'administrator' ) ) {
	wp_die(
		__( "We're sorry, you're not allowed to access Disk Usage." )
	);
}
$template_path = !$usage_stats_exist ? 'templates/admin-results.php' : 'templates/admin-instructions.php';
?>

<div class="wrap">
	<h1 class="wp-heading-inline">Disk Usage Plugin</h1>
	<h2 class="nav-tab-wrapper">
		<a href="#tab1" class="nav-tab nav-tab-active">Results</a>
		<a href="#tab2" class="nav-tab">Controls</a>
	</h2>
	<div id="tab1" class="tab-panel">
		<?php require_once $this->plugin_path . $template_path?>
	</div>
	<div id="tab2" class="tab-panel">
		<?php require_once $this->plugin_path . 'templates/controls.php'?>
	</div>
</div>





