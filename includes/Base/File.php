<?php

namespace includes\Base;

class File{

	public $id ;
	private static $counter = 0;

	public $path;
	public $name;
	public $extension ;

	public $size = 0 ;
	public $sizeOnDisk = 0 ;  // because the space occupied on the disk is different from the real file size
	public $percent = 0;

	public $isDir = false;

	public $parent ;
	public $parentsIds = array() ;
	public $children ;
	public $depth = 0 ;

	public $itemsCount = 0 ;
	public $filesCount = 0 ;
	public $subdirsCount = 0 ;

	public $error = false ;

	public $lastModificationTime ;


	/**
	 * constructor
	 *
	 * @param string $path absolute or relative path of the file
	 */
	public function __construct( string $path){
		$this->id = self::$counter++;
		$this->path = $path;
		$this->name = basename($this->path);
		$this->extension = pathinfo($this->path, PATHINFO_EXTENSION);
		$this->isDir = is_dir($this->path) ;
		$this->size = ($this->isDir) ? 0: filesize($this->path);

		$stats = stat($path);
		if($stats){
			$this->sizeOnDisk = $stats['blocks'] * 512;
			$this->lastModificationTime = $stats['mtime'];
		}
	}


	/**
	 * Add a file to the folder
	 *
	 * @param File $file
	 */
	public function addFile( File $file){
		if($this->children === null)
			$this->children = [];
		$this->children[]= $file ;
		// set depth
		$file->depth = $this->depth + 1 ;

		// set parent of the file
		$file->parent = $this ;
	}

}