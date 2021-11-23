<?php
namespace Hsolc\Messaging\Mail;
use Messaging\Mail\MailMessageForm;

class HsolcAnnouncementMailMessageForm extends \Messaging\Mail\MailMessageForm {

	public function __construct()
	{
		parent::__construct();
		$this->textBody(getTextBody());
		$this->htmlBody(getHtmlBody());
		$this->action = $this->action == 'Send Mail Now' ? 'sendAnnouncements' : 'viewAnnouncements';
		$this->init();
	}
	public function getRecipients()
	{
		return $this->recipients;
	}
	private function init()
	{
		switch($this->get('recipients'))
		{
			case 'sami':
				$r= 'sbower@hsolc.org,lfallen1@q.com';
				break;
			case 'jose':
				$r= 'jbernal.web.dev@gmail.com';
				break;
			case 'test_group':
				$r= 'sbower@hsolc.org,mstiner@hsolc.org,jbernal.web.dev@gmail.com,lfallen1@q.com';
				break;
			case 'everyone':
				$r = 'everyone@hsolc.org';
				break;
			default:
				$r = 'jbernal.web.dev@gmail.com';
		}
		$this->recipients = $r;
	}
	
	public function render()
	{
		$form = "<form onsubmit='return mailFormChecker();' method='post'>";
		$form .= "<div class='form-item'><label for='emailGroup'>Send to:</label><select id='recipients' name='recipients'>
		<option value='sami'>Sami only</option> 
		<option value='jose'>José only</option> 
		<option selected='selected' value='test_group'>Sami, Mel and José</option>
		<option value='everyone'>HSOLC Staff</option>
		</select></div>";
		$form .= "<div class='form-item'><input style='font-size:18px;padding:5px;' type='submit' id='action' name='action' value='Send Mail Now' /></div>";
		$form .= "<div class='form-item'><label for='mailMessageBodyPreview'>A Preview of your mail message appears below:</label><div style='border:1px solid #666; padding:5px; width:60%;height:400px;overflow:scroll;' id='mailMessageBodyPreview'>".$this->getMailMessageHtmlBody()."</div></div>";
		$form .= "</form>";
		return $form;
	}
	public function getMailMessageTextBody()
	{
		return $this->textBody;
	}
	public function getMailMessageHtmlBody()
	{
		return $this->htmlBody;
	}
	protected function submit()
	{
		header('Location: '.$_PHP['SELF']);
		// $this new mail message blah blah blah...
	}
	
}