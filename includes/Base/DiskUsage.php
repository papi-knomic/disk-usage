<?php

namespace includes\Base;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;

class DiskUsage extends BaseController
{
	public function register(): void
	{
		add_action('wp_ajax_gather_disk_usage_results', [ $this, 'gatherDiskUsageResults']);
		add_action('wp_ajax_nopriv_gather_disk_usage_results', [ $this, 'gatherDiskUsageResults']);
	}

	public function gatherDiskUsageResults(): void
	{
		$progress = sanitize_text_field($_POST['progress']);
		$usage_stats = $this->scanDisk($progress);
		wp_send_json($usage_stats);
	}

	private function scanDisk( int $progress )
	{
		global $wpdb;

		if ($progress == 0) {
			$this->truncateTable($wpdb->prefix . FILE_DATA_TABLE);
			$this->truncateTable($wpdb->prefix . JOB_STATE_TABLE);
		}

		$chunk = 100;
		$workerTime = get_option('disk_usage_worker_time', 5);
		$startTime = time();
		$totalFiles = $this->countFilesInDirectory(ABSPATH);
		$currentFile =  $this->getCurrentItem();
		$files = $this->getFileChunk($currentFile, $chunk);
		$fileCount = 0;

		foreach ($files as $file ) {
			$fileCount++;
			$currentFile++;
			$this->saveFileData( $file, $wpdb);
			$this->saveJobState($currentFile, $totalFiles, $wpdb);

			$elapsedTime = time() - $startTime;

			if ($elapsedTime >= $workerTime || $currentFile == $totalFiles || $fileCount == count($files) ){
				return [
					'total' => $totalFiles,
					'progress' => $currentFile
				];
			}
		}

		return [
			'total' => 100,
			'progress' => 100
		];
	}

	private function truncateTable(string $tableName) : void
	{
		global $wpdb;
		$wpdb->query("TRUNCATE TABLE $tableName");
	}

	private function getFileChunk($start, $count): array
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

	private function getCurrentItem(): ?int
	{
		global $wpdb;

		$table_name = $wpdb->prefix . JOB_STATE_TABLE;
		$query = $wpdb->prepare("SELECT current_file FROM $table_name");

		return $wpdb->get_var($query) ?? 0;
	}

	private function countFilesInDirectory($path): int {
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

		return iterator_count($iterator);
	}


	private function saveFileData($item, $wpdb)
	{
		$path = $item->getPath();
		$realPath = $item->getPathname();

		if (strpos($realPath, "..") !== false) {
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

			$data = array(
				'file_path' => $realPath,
				'parent_path' => $parent,
				'size' => $size,
				'file_count' => $fileCount,
				'created_at' => current_time('mysql'),
			);

			$wpdb->insert($table_name, $data);
		}
	}

	private function saveJobState( int $currentFile, int $totalFiles, $wpdb ) : void
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

	private function getFolderSize($folderPath): int
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


}