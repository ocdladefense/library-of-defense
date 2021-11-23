<?php

namespace PhpUtilities\DateFormats;

class DateFormat {
	protected $format = 'Y-m-j';
	
	public static function CreateFormat($DateFormatIdentifier){
		$class = "PhpUtilities\\DateFormats\\{$DateFormatIdentifier}DateFormat";
		return new $class();
	}
	public function getFormat(){
		return $this->format;
	}
}