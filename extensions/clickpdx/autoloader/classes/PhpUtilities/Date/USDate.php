<?php

namespace PhpUtilities\Date;

use PhpUtilities\DateFormats\DateFormat as DateFormat;
use PhpUtilities\DateFormats\DefaultDateFormat as DefaultDateFormat;

class USDate extends \DateTime {

	public function __construct($dateString){
		parent::__construct($dateString);
	}
	public function setDate($dateString){
		if(!isset($dateString)){
			throw new InvalidDateFormatException('Invalid Date format.');
		}
		$parts=explode("/",$dateString);
		parent::setDate($parts[2],$parts[0],$parts[1]);
		return $this;
	}
	public function getFormat(\DateFormat $format){
		return parent::format($format->getFormat());
	}
	public function __toString(){
		return $this->format(new DefaultDateFormat);
	}
}