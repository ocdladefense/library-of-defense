<?php

namespace Drupal\Forms;

class NodeEditForm extends Form {
	
	public function __construct(&$form=null,$form_id){
		if(!isset($form['#node'])){
			throw new InvalidNodeEditFormException("(Form_id {$form_id}): Instance of NodeEditForm requires a node to be attached to this form's #node property.");
		}
		parent::__construct($form,$form_id);
		$this->node=$form['#node'];
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
  public function addElement($type){
  	try {
  		$type="\\Drupal\\Elements\\".$type."Element";
			$elem = new $type();
			$elem->setForm($this);
  	} catch(Exception $e){
  		// drupal_set_message($e->getMessage(),'warning',false);
  		// class not found exception
  	}
		$this->elements[] = $elem;
    return $elem;
	}
	public function getOriginalForm(){
		return $this->form;
	}
	public function rebuildForm(){
		return array_merge($this->form,$this->getElements());
	}
	public function getElements(){
		$elems=array();
		foreach($this->elements as $elem){
			$elems+= $elem->toDrupal();
		}
		return $elems;
	}

}