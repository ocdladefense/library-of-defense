<?php

namespace Drupal\JS;

class JavaScript {

	private $data;
	
	private $type = 'file';
	
	private $scope = 'header';
	
	private $defer = false;
	
	private $cache = false;
	
	private $preprocess = false;
	
	public function __construct($arg){
		if(gettype($arg)=='string'){
			$this->data = $arg;
			$this->type = 'inline';
		}
		if(get_class($arg)=='Drupal\JS\ScriptFile'){
			$this->data = $arg->getRelativePath();
		}
	}
	
	public function inject(){
		\drupal_add_js(
			$this->data,
			$this->type,
			$this->scope,
			$this->defer,
			$this->cache,
			$this->preprocess
		);
	}
	public function __toString(){
		return "{$this->scope}: {$this->data}";
	}

}  