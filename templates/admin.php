<?php

//echo '<div class="wrap">';
//echo '<h1>Disk Usage Plugin</h1>';
//
//echo '<h2>Results</h2>';
//if ($usage_stats_exist) {
//	echo '<p>Gathered disk usage stats:</p>';
//} else {
//	echo '<p>No usage stats found. Please click the button below to start gathering results.</p>';
//}
//
//echo '<h2>Controls</h2>';
//echo '<button id="gather-results-btn">Gather Results</button>';
//
//echo '</div>';

?>

<div class="wrap">
	<h1 class="wp-heading-inline">Disk Usage Plugin</h1>
	<h2 class="nav-tab-wrapper">
		<a href="#tab1" class="nav-tab nav-tab-active">Results</a>
		<a href="#tab2" class="nav-tab">Controls</a>
	</h2>
	<div id="tab1" class="tab-panel">
		<!-- Content for Tab 1 goes here -->
	</div>
	<div id="tab2" class="tab-panel">
		<!-- Content for Tab 2 goes here -->
	</div>
</div>


<script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();

            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            $('.tab-panel').hide();
            $($(this).attr('href')).show();
        });
    });
</script>





