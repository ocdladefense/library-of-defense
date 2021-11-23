<?php


namespace Drupal\JS;

class ScriptManager {

	private $scope;
	
	private $scripts;

	public function __construct($scope = 'header'){
		$this->scope = $scope;
	}
	public function add($args){
		if(is_array($args)){}
		// print get_class($args);exit;
		if(get_class($args)=='Drupal\JS\ScriptFile'){	
			$s = new JavaScript($args);
		}
		elseif(gettype($args)=='string'){
			$s = new JavaScript($args);
		}
		else {
			print $this;
			throw new \Exception("Invalid parameter, {$args}, for ".__CLASS__);
		}
		$this->scripts[]=$s;
		$s->inject();
	}
	public function __toString(){
		$out = 'Dumping info for '.__CLASS__. "\n";
		if(!count($this->scripts)) $out .= "No scripts found!";
		foreach($this->scripts as $script){
			$out .= $script->__toString() . "\n";
		}
		return $out;
	}
}