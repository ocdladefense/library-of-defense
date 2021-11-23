<?php

namespace PhpUtilities;

class UtilityArray {
	const ARRAY_EXPLODE_DELIMITER = ',';
	
	public $data = array();
	
	public function __construct(Array $data){
		$this->data=$data;
	}
	public static function NewArray($data){
		$arr = new UtilityArray($data);
		return $arr;
	}
	public function has($value){
		return in_array($value,$this->data);
	}
	public function hasExactly($value){
		return in_array($value,$this->data,true);	
	}
	public static function StringToArray($str){
		$data=explode(self::ARRAY_EXPLODE_DELIMITER,ISODATE_NODE_TYPES);
		return self::NewArray($data);
	}
}