<?php

namespace includes\Base;

class DiskUsage {

	private function calculateDiskUsage($directory): array
	{
		$total_usage = 0;
		$file_types = [];

		// Recursively calculate the disk usage of files and directories
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
		foreach ($iterator as $file) {
			// Exclude certain file types if needed
			if ($this->shouldExcludeFileType($file)) {
				continue;
			}

			$size = $file->getSize();
			$total_usage += $size;

			// Group the files by file type
			$file_type = $file->getExtension();
			if (!isset($file_types[$file_type])) {
				$file_types[$file_type] = 0;
			}
			$file_types[$file_type] += $size;
		}

		// Sort the file types by size in descending order
		arsort($file_types);

		// Convert the sizes to human-readable format
		$total_usage_formatted = $this->formatSize($total_usage);
		foreach ($file_types as $file_type => &$size) {
			$size = $this->formatSize($size);
		}
		$total = disk_total_space('/');
		// Return the disk usage statistics
		return [
			'total_usage' => $total_usage_formatted,
			'file_types' => $file_types,
			'percentage' => ($total_usage/$total)*100,
			'total_size' => $this->formatSize($total)
		];
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