<?php

namespace Drupal\Elements;

class IsoDateElement extends ElementBase {

	public function __construct($name,$value){
		$this->name = isset($name) ? $name : 'iso_date';
		parent::__construct($name,$value);
	}
	public function toDrupal(){
    return array(
    	$this->name => array(
    		'#title' => t('!title',array('!title'=>$this->title)),
				'#type' => $this->type,
				'#default_value' => $this->default_value,
				'#maxlength' => 25,
				'#size' => 10,
				'#weight' => $this->weight,
				'#description' => t('!description',array('!description'=>$this->description)),
      )
    );
	}
	
}