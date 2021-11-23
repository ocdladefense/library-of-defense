<?php

namespace Drupal\JS;

class ScriptFile extends \FileSystem\File {
	private $relativePath;
	
	public function __construct(String $relativePath){
		$fullPath = \autoloader_document_root() .'/'.$relativePath;
		if(isset($relativePath)&&!file_exists($fullPath)) throw new \Exception("File, $fullPath, not found.");
		parent::__construct($fullPath);
		$this->relativePath = $relativePath;
	}
	public function getRelativePath(){
		return $this->relativePath;
	}
}