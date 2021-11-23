<?php

namespace Drupal\Elements;

class FieldsetElement extends ElementBase {//implements DrupalRenderable {

	public function __construct($name,$value){
		parent::__construct($name,$value);
	}	
	private function lookupValue($key){
		$result=\db_result(\db_query("SELECT isodate FROM {clickpdx_iso_announcement_date} WHERE nid = %d LIMIT 1",$key));
		return $result;
	}
	public function storeValue(){
		// https://api.drupal.org/comment/132#comment-132
	}
	public function toDrupal(){
    $fieldset=array(
    	$this->getName() => array(
				'#type' => 'fieldset',
				'#title' => t('!title',array('!title'=>$this->title)),
				'#description' => t('!description',array('!description'=>$this->description)),
				'#collapsible' => true,
				'#collapsed' => false,
				'#access' => true,//user_access('create url aliases'),
				'#weight' => $this->weight,
      )
    );
    foreach($this->elements as $elem){
    	$fieldset[$this->getName()]+=$elem->toDrupal();
    }
  	return $fieldset;
  }
}