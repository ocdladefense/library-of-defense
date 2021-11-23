<?php

namespace Drupal\Nodes;

use PhpUtilities\Date\USDate as USDate;
use PhpUtilities\DateFormats\DateFormat;

class AnnouncementNode extends NodeBase {

	protected $date;
	
	protected $type;
	
	public function __construct($node){
		parent::__construct($node);
		$this->init();
	}
	private function init(){
		$this->load();
	}

	public function formAlter($form){
		$form->addElement('Fieldset')->setName('iso_date')->setTitle('Announcement Date')->value('Announcement Date')->weight(-10)
		->addElement('IsoDate')->setTitle('Announcement Date')->setName('iso_date')->value($this->getDisplayDate());
		//	print_r($thisForm->getForm());exit;
	}

}