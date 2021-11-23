<?php
namespace Hsolc\Messaging\Mail;
use Messaging\Mail\MailMessage;

class HsolcAnnouncementMailMessage extends \Messaging\Mail\MailMessage {

	public function __construct($to)
	{
		parent::__construct();
		$this->recipients = $this->formatEmailList($to);
		$this->subject = 'LOD Contact Form';
		$this->addMailHeader('From',"LOD website <admin@lodtest.ocdla.org>");
		$this->addMailHeader('Reply-To',"Admin <info@ocdla.org>");
		$this->addMailHeader('Return-Path',"LOD website <admin@lodtest.ocdla.org>");
		$this->addParameter("-f admin@lodtest.ocdla.org");
	}
	private function formatEmailList($addresses)
	{
		return is_array($addresses)?implode(',',$addresses):$addresses;
	}
	public function sendAnnouncements()
	{
		// $this->recipients = 'jbernal.web.dev@gmail.com,redderx@yahoo.com';
		// print $this->recipients;
		return $this->sendWithValues($this->recipients, $this->subject, $this->getMultiBody());
	}
	
}