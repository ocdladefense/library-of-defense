<?php

namespace Drupal\Forms;

class Fieldset {
	protected $elements;
	
	
	// An internal array of elements added to this form.
	protected $elements;
	
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