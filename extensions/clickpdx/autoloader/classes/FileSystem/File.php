<?php

namespace FileSystem;

class File {

	protected $filepath;
	
	protected $base_path;
	
	public function __construct($filepath){
		$this->filepath = $filepath;	
	}
	public function getFilePath(){
		return $this->filepath;
	}
	public function setBasePath($basepath){
		$this->basepath = $basepath;
	}
}