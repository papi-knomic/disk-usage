<?php

namespace includes\Base;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class DiskUsage
 * @package includes\Base
 */
class DiskUsage extends BaseController
{
	/**
	 * Register hooks and actions
	 */
	public function register(): void
	{
		add_action('wp_ajax_gather_disk_usage_results', [$this, 'gatherDiskUsageResults']);
		add_action('wp_ajax_nopriv_gather_disk_usage_results', [$this, 'gatherDiskUsageResults']);
	}

	/**
	 * Gather disk usage results via AJAX
	 */
	public function gatherDiskUsageResults(): void
	{
		$progress = sanitize_text_field($_POST['progress']);
		$usage_stats = $this->scanDisk($progress);
		wp_send_json($usage_stats);
	}

	/**
	 * Scan the disk to calculate usage statistics
	 *
	 * @param int $progress The progress value
	 * @return array The usage statistics
	 */
	private function scanDisk(int $progress): array
	{
		global $wpdb;

		if ($progress === 0) {
			$this->truncateTable($wpdb->prefix . FILE_DATA_TABLE);
			$this->truncateTable($wpdb->prefix . JOB_STATE_TABLE);
		}

		$usage_stats_exist = get_option('disk_usage_stats_exists');
		$fileTypesData = get_option('disk_usage_file_types', []);

		if (!(bool)$usage_stats_exist) {
			update_option('disk_usage_stats_exists', true);
		}

		$chunk = 100;
		$workerTime = get_option('disk_usage_worker_time', 5);
		$startTime = time();
		$totalFiles = $this->countFilesInDirectory(ABSPATH);
		$currentFile = $this->getCurrentItem();
		$files = $this->getFileChunk($currentFile, $chunk);
		$fileCount = 0;

		foreach ($files as $file) {
			$fileCount++;
			$currentFile++;
			$this->saveFileData($file, $wpdb);
			$this->saveJobState($currentFile, $totalFiles, $wpdb);

			// Get the file extension
			$fileExtension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
			$fileSize = $file->getSize();

			// Update the count and size for the file extension
			if (!empty($fileExtension)) {
				if (isset($fileTypesData[$fileExtension])) {
					$fileTypesData[$fileExtension]['count']++;
					$fileTypesData[$fileExtension]['size'] += $fileSize;
				} else {
					$fileTypesData[$fileExtension] = array(
						'count' => 1,
						'size' => $fileSize
					);
				}
			}

			$elapsedTime = time() - $startTime;

			if ($elapsedTime >= $workerTime || $currentFile == $totalFiles || $fileCount == count($files)) {
				update_option('disk_usage_file_types', $fileTypesData);
				return [
					'total' => $totalFiles,
					'progress' => $currentFile
				];
			}
		}

		update_option('disk_usage_file_types', $fileTypesData);

		return [
			'total' => 100,
			'progress' => 100
		];
	}

	/**
	 * Truncate a database table
	 *
	 * @param string $tableName The name of the table to truncate
	 */
	private function truncateTable(string $tableName): void
	{
		global $wpdb;
		$wpdb->query("TRUNCATE TABLE $tableName");
	}

	/**
	 * Get a chunk of files from a given start position
	 *
	 * @param int $start The start position
	 * @param int $count The number of files to retrieve
	 *
	 * @return array The files in the chunk
	 */
	private function getFileChunk( int $start, int $count): array
	{
		$files = [];
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ABSPATH));
		$fileCount = 0;
		$doneCount = 0;

		foreach ($iterator as $file) {
			$fileCount++;
			if ($fileCount >= $start) {
				$files[] = $file;
				$doneCount++;
				if ($doneCount >= $count) {
					break;
				}
			}
		}

		return $files;
	}

	/**
	 * Get the current item from the database job state
	 *
	 * @return int|null The current item or null if not found
	 */
	private function getCurrentItem(): ?int
	{
		global $wpdb;

		$table_name = $wpdb->prefix . JOB_STATE_TABLE;
		$query = $wpdb->prepare("SELECT current_file FROM $table_name");

		return $wpdb->get_var($query) ?? 0;
	}

	/**
	 * Count the number of files in a directory recursively
	 *
	 * @param string $path The directory path
	 *
	 * @return int The number of files
	 */
	private function countFilesInDirectory( string $path): int
	{
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

		return iterator_count($iterator);
	}

	/**
	 * Save file data to the database
	 *
	 * @param mixed $item The file or directory item
	 * @param mixed $wpdb The WordPress database object
	 */
	private function saveFileData($item, $wpdb)
	{
		$path = $item->getPath();
		$realPath = $item->getPathname();

		if (str_contains($realPath, "..")) {
			return;
		}

		if (ABSPATH != $realPath) {
			$parent = (ABSPATH != $path) ? $path : "";
			if ($item->isDir()) {
				$lastSlashPosition = strrpos($path, '/');
				if ($lastSlashPosition !== false) {
					$parent = substr($path, 0, $lastSlashPosition);
				}
			}

			$size = ($item->isFile()) ? $item->getSize() : $this->getFolderSize($realPath);
			$fileCount = ($item->isDir()) ? $this->countFilesInDirectory($realPath) : 0;
			$table_name = $wpdb->prefix . FILE_DATA_TABLE;

			$data = [
				'file_path' => $realPath,
				'parent_path' => $parent,
				'size' => $size,
				'file_count' => $fileCount,
				'created_at' => current_time('mysql'),
			];

			$wpdb->insert($table_name, $data);
		}
	}

	/**
	 * Save the current job state to the database
	 *
	 * @param int $currentFile The current file number
	 * @param int $totalFiles The total number of files
	 * @param mixed $wpdb The WordPress database object
	 */
	private function saveJobState(int $currentFile, int $totalFiles, $wpdb): void
	{
		$table_name = $wpdb->prefix . JOB_STATE_TABLE;

		$data = [
			'current_file' => $currentFile,
			'total_files' => $totalFiles,
			'created_at' => current_time('mysql'),
		];

		$row = $wpdb->get_row("SELECT * FROM $table_name");

		if ($row) {
			$wpdb->update($table_name, $data, ['id' => $row->id]);
		} else {
			$wpdb->insert($table_name, $data);
		}
	}

	/**
	 * Get the size of a folder recursively
	 *
	 * @param string $folderPath The folder path
	 *
	 * @return int The total size of the folder in bytes
	 */
	private function getFolderSize( string $folderPath): int
	{
		$totalSize = 0;

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($folderPath),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$totalSize += $file->getSize();
			}
		}

		return $totalSize;
	}

	public function generateFileTree(): array
	{
		global $wpdb;
		$table_name = $wpdb->prefix . FILE_DATA_TABLE; // Replace 'your_table_name' with the actual table name

		// Query the database to retrieve the file tree data
		$query = "SELECT * FROM $table_name ORDER BY file_path ASC";
		$results = $wpdb->get_results($query, ARRAY_A);

		// Prepare the file tree structure
		$fileTree = array();
		foreach ($results as $result) {
			$filePath = $result['file_path'];
			$parentPath = $result['parent_path'];
			$size = $result['size'];

			// Create an array for the file or directory
			$node = array(
				'name' => basename($filePath),
				'size' => $size
			);

			// Find the parent directory in the file tree
			if ($parentPath === '') {
				// If the parent path is empty, it is the root directory
				$fileTree[] = $node;
			} else {
				// Traverse the file tree to find the parent directory
				$currentNode = &$fileTree;
				$parentPathSegments = explode('/', $parentPath);
				foreach ($parentPathSegments as $segment) {
					$found = false;
					foreach ($currentNode as &$childNode) {
						if ($childNode['name'] === $segment) {
							$currentNode = &$childNode['children'];
							$found = true;
							break;
						}
					}
					if (!$found) {
						// If the parent directory does not exist, create it
						$newNode = array(
							'name' => $segment,
							'children' => array($node)
						);
						$currentNode[] = $newNode;
						break;
					}
				}
			}
		}

		return $fileTree;
	}


	public function getFileData(): array{
		// Assuming you have a database connection established and the appropriate query executed
		$result = mysqli_query($conn, "SELECT * FROM FILE_DATA_TABLE");

// Create an empty array to store the table data
		$tableData = array();

// Fetch each row from the result set and add it to the array
		while ($row = mysqli_fetch_assoc($result)) {
			$tableData[] = $row;
		}

// Print the array to verify the data
		print_r($tableData);
		return $tableData;
	}

}