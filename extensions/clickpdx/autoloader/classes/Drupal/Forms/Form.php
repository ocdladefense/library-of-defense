<?php

use Drupal\Elements\ElementBase;
use Drupal\Elements\FieldsetElement;
use Drupal\Elements\IsoDateElement;
 
namespace Drupal\Forms;

class Form extends \Drupal\Elements\ElementBase {
	
	protected $values;

	public function __construct(&$form=null,$form_id){
		if(!isset($form_id)){
			throw new InvalidFormException("Cannot instantiate object of class, ".__CLASS__.", without a \$form_id");
		}
		$this->form=$form;
	}

	public function getNode($prop){
		return $this->node;
	}
	
	public function getValue($name){
		if(!isset($this->values[$name])){
			throw new Exception("Value {$name} not found.");
		}
		else return $this->values[$name];
	}
	
	public function isNodeEditForm(){
		if (isset($this->form['type']) && isset($this->form['#node']) && $this->form['type']['#value'] . '_node_form' == $form_id){
			return true;
		}
	}

}