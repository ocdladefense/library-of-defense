<?php

namespace Drupal\Elements;

class ElementBase {
	protected $form;
	
	protected $elements = array();
	
	protected $type = "textfield";
	
	protected $name;
	
	protected $value;
	
	protected $title;
	
	protected $default_value;
	
	protected $weight = 0;
	
	protected $node;
	
	protected $description;
	
	public function __construct($name,$value) {
		if(isset($name)) $this->name=$name;
		if(isset($value)) $this->value=$value;
	}
	public function setForm(NodeEditForm $form){
		$this->form=$form;
	}
	public function setName($name){
		$this->name=$name;
		return $this;
	}
	public function getName($name){
		return $this->name;
	}
	public function setTitle($title){
		$this->title=$title;
		return $this;
	}
	public function getTitle(){
		return $this->title;
	}
	public function value($value){
		$this->default_value=$value;
		return $this;
	}
	public function weight($weight){
		$this->weight=$weight;
		return $this;
	}
	public function setType($type){
		$this->type=$type;
	}
  public function addElement($type){
  	try {
  		$type="\\Drupal\\Elements\\".$type."Element";
			$elem = new $type();
			$elem->setForm($this->form);
  	} catch(Exception $e){
  		drupal_set_message($e->getMessage(),'warning',false);
  		// class not found exception
  	}
		$this->elements[] = $elem;
    return $elem;
	}
}