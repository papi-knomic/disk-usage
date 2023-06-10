<?php

namespace includes\Base;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Ajax extends BaseController
{
	public function register(): void
	{
		add_action('wp_ajax_gather_disk_usage_results', [ $this, 'gatherDiskUsageResults']);
		add_action('wp_ajax_nopriv_gather_disk_usage_results', [ $this, 'gatherDiskUsageResults']);
	}
	public function gatherDiskUsageResults(): void
	{
		$root_directory = ABSPATH;
		$usage_stats = $this->calculateDiskUsage($root_directory);
		wp_send_json($usage_stats);
	}

	private function calculateDiskUsage($directory): array {
		$usage_stats_exist = get_option('disk_usage_stats_exists');
		$total = disk_total_space('/');
		$total_usage = 0;
		$result = [];

		$items = scandir($directory);

		foreach ($items as $item) {
			// Exclude current directory (.) and parent directory (..)
			if ($item !== '.' && $item !== '..') {
				$itemPath = $directory . '/' . $item;

				if (is_dir($itemPath)) {
					$subfolder_data = $this->calculateDiskUsage($itemPath);
					$data = [
						'path' => $itemPath,
						'size' => $subfolder_data['total_size'],
						'percentage' => $subfolder_data['percentage'],
						'subfolders' => $subfolder_data['directories'],
						'files' => $subfolder_data['files']
					];
					$result['directories'][] = $data;
					$total_usage += $subfolder_data['total_usage'];
				} else {
					$size = filesize($itemPath);
					$data = [
						'path' => $itemPath,
						'size' => $this->formatSize($size),
						'percentage' => ($size / $total) * 100
					];
					$result['files'][] = $data;
					$total_usage += $size;
				}
			}
		}

		$result['percentage'] = ($total_usage / $total) * 100;
		$result['total'] = $this->formatSize($total);
		$result['total_size'] = $this->formatSize($total_usage);

		return $result;
	}


	// Function to get the directory size recursively
	public function getDirectorySize($directory) {
		$totalSize = 0;
		$files = scandir($directory);

		foreach ($files as $file) {
			if ($file !== '.' && $file !== '..') {
				$filePath = $directory . '/' . $file;

				if (is_dir($filePath)) {
					$totalSize += $this->getDirectorySize($filePath);
				} else {
					$totalSize += filesize($filePath);
				}
			}
		}

		return $totalSize;
	}



	private function getSubfolders($directory): array
	{
		$subfolders = [];
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
		foreach ($iterator as $file) {
			if ($file->isDir()) {
				$subfolders[] = $file->getPathname();
			}
		}
		return $subfolders;
	}

	private function getFiles($directory): array
	{
		$files = [];
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$files[] = [
					'name' => $file->getFilename(),
					'size' => $this->formatSize($file->getSize())
				];
			}
		}
		return $files;
	}

	private function shouldExcludeFileType(SplFileInfo $file): bool
	{
		// Define the file types to exclude from the disk usage calculations
		$excluded_file_types = ['tmp', 'log'];

		// Exclude files with specified extensions
		$extension = $file->getExtension();
		if (in_array($extension, $excluded_file_types)) {
			return true;
		}

		return false;
	}

	private function formatSize($size): string
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];
		$index = 0;

		while ($size >= 1024 && $index < count($units) - 1) {
			$size /= 1024;
			$index++;
		}

		return round($size, 2) . ' ' . $units[$index];
	}
}