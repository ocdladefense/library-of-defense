<?php
namespace Messaging\Mail;
use Http\HttpRequest;

class MailMessageForm extends \Http\HttpRequest {


	protected $recipients;
	
	private $from;
	
	private $subject;
	
	private $headers;
	
	protected $htmlBody;
	
	protected $textBody;
	
	private $boundary; 
	
	private $multipart = false;
	
	protected $action;

	public function __construct()
	{
		parent::__construct();
		$this->action = isset($_POST['action']) ? $_POST['action'] : 'view';
		$this->recipients = isset($_POST['recipients']) ? $_POST['recipients'] : null;
	}
	protected function get($prop)
	{
		if(isset($this->{$prop})) return $this->{$prop};
		else return false;
	}
	public function action()
	{
		return $this->action;
	}
	public function textBody($str)
	{
		$this->textBody = $str;	
	}
	public function htmlBody($str)
	{
		$this->htmlBody = $str;
	}
	protected function submit()
	{
		// $this new mail message blah blah blah...
	}
	
}