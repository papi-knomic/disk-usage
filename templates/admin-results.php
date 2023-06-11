<?php
$fileTypesData = get_option('disk_usage_file_types');

if (!empty($fileTypesData)) {
	// Sort the file types data by size in descending order
	function sortBySize($a, $b) {
		if ($a['size'] == $b['size']) {
			return 0;
		}
		return ($a['size'] > $b['size']) ? -1 : 1;
	}

	// Sort the file types data using the custom sorting key function
	uasort($fileTypesData, 'sortBySize');

	// Define the number of rows to display initially and the total number of rows
	$rowsToShow = 5;
	$totalRows = count($fileTypesData);

	echo '<div class="file-types-table-container">';
	echo '<button id="toggle-button" class="button">Show More</button>';
	echo '<table class="wp-list-table widefat fixed striped">';
	echo '<thead><tr><th scope="col">Extension</th><th scope="col">Count</th><th scope="col">Size</th></tr></thead>';
	echo '<tbody>';

	$rowCount = 0;
	foreach ($fileTypesData as $extension => $data) {
		if ($rowCount >= $rowsToShow) {
			echo '<tr class="hidden">';
		} else {
			echo '<tr>';
		}

		echo '<td>' . $extension . '</td>';
		echo '<td>' . $data['count'] . '</td>';
		echo '<td>' . formatBytes($data['size']) . '</td>';
		echo '</tr>';

		$rowCount++;
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';
} else {
	echo 'No file types data available.';
}
