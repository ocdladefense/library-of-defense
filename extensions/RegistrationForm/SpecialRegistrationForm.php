<?php
use Messaging\Mail\MailMessage as MailMessage;
use Hsolc\Messaging\Mail\HsolcAnnouncementMailMessage as HsolcAnnouncementMail;
use Hsolc\Messaging\Mail\HsolcAnnouncementMailMessageForm as HsolcAnnouncementMailMessageForm;
use Http\HttpRequest as HttpRequest;


class SpecialRegistrationForm extends SpecialPage {
	private $htmlFile = 'RegistrationForm.tpl.php';

	private $user;

	private $htmlEmailTemplate = 'Email.tpl.php';
	
	function __construct() {
		parent::__construct( 'RegistrationForm' );
		$this->user = User::newFromSession();
		$this->user->load();
		$this->user->isAdmin = $this->isAdmin();
	}
	function isAdmin()
	{
		return in_array('legcomm',$this->user->mGroups);
	}
	function getUserSignature()
	{
		return $this->user->mRealName . "<br />" . $this->user->mEmail;
	}
	function execute( $par ) {
		$r = $this->getRequest();
		$o = $this->getOutput();
		// print get_class($par);
		$action = $r->getText('formAction');
		switch($action)
		{
			case 'sendEmail':
				try
				{
					$this->sendMail();
					$this->getThankYouPage();
				}
				catch(Exception $e)
				{
					$this->getErrorPage();
				}
				break;
			case 'preview':
				$this->getPreview();
				break;
			default:
				$this->getContactPage();
		}
	}

	function getPreview()
	{
		$request = $this->getRequest();
		$output = $this->getOutput();
		$includeUserInfo = false;
		$out = '';
		if($includeUserInfo)
		{
			$out = "<pre>".print_r($this->user,true)."</pre>";
		}
		print $out . $this->prepareEmailPreview();
		exit;
	}
	function sendMail()
	{
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->emailContactForm();
	}
	function getThankYouPage()
	{
		$request = $this->getRequest();
		$output = $this->getOutput();
		$output->addHTML("<h2>Thanks for contacting us.</h2>");
	}
	function getContactPage()
	{
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();
 
 		$this->formType = $request->getText('type')=='suggest'?'suggest':'issue';
 		$this->formTitle = $request->getText('type')=='suggest'?'Make a suggestion':'Notify us of an issue';
 		$testRequest = false;
 		if($testRequest)
 		{
 			$markup = "<pre>".print_r($request,true)."</pre>";
 			$output->addHTML($markup);
 			return;
 		}
 		
		# Do stuff
		// $output->addWikiText("This is some ''lovely'' [[wikitext]] that will '''get''' parsed nicely.");
		
		$htmlLoader = new FileLoader($this->htmlFile);
		$output->addHTML($this->getContactFormMarkup($htmlLoader));
		$output->addStyle('/extensions/RegistrationForm/contact.css?v=0.1', 'all');
		$output->addScriptFile('/extensions/RegistrationForm/form.js');
	}
	private function emailContactForm()
	{
		$htmlVersion = $this->prepareEmailHtmlBody();
		$textVersion = $this->prepareEmailTextBody();
		$mailMessage = new HsolcAnnouncementMail(
			array(
				'jbernal.web.dev@gmail.com',
				'abassos@mpdlaw.com',
				'tmay@ocdla.org'
			)
		);
		$mailMessage->multipart(true);
		$mailMessage->textBody($textVersion);
		$mailMessage->htmlBody($htmlVersion);
		$mailMessage->sendAnnouncements();
	}
	private function getContactForm()
	{
		return $this->prepareEmailHtmlBody();
	}
	private function getIssueFields()
	{
		$r = $this->getRequest();
		return array(		
			'Issue Type' => $r->getText('issueCat'),
			'Issue Description' => $r->getText('issueDesc'),		
			'Signed' => $r->getText('signMe') ? 
					$this->getUserSignature() : 
					'<span style="font-style:italic;">This request was submitted anonymously.</span>',
		); 
	}
	private function getSuggestionFields()
	{
		$r = $this->getRequest();
		return array(		
			'Suggestion Type' => $r->getText('issueCat'),
			'Suggestion Description' => $r->getText('issueDesc'),		
			'Signed' => $r->getText('signMe') ? 
					$this->getUserSignature() : 
					'<span style="font-style:italic;">This request was submitted anonymously.</span>',
		); 
	}
	private function prepareEmailTextBody()
	{
		return "A text-only version of this issue report could appear here.";
	}
	private function prepareEmailPreview()
	{
		$r = $this->getRequest();
		$loader = new FileLoader('Email.tpl.php');
		$fields = $r->getText('type')=='suggest'?
			$this->getSuggestionFields():
			$this->getIssueFields();
		return $loader->render(array(
			'context'=>'preview',
			'emailFrom' => 'LOD website',
			'emailSubject' => 'LOD website issue',
			'vars'=>$fields
		));
	}
	private function prepareEmailHtmlBody()
	{
		$loader = new FileLoader('Email.tpl.php');
		$issueFields = $this->getIssueFields();
		return $loader->render(array('context'=>'send','vars'=>$issueFields));
	}

		/**
     * Override the parent to set where the special page appears on Special:SpecialPages
     * 'other' is the default, so you do not need to override if that's what you want.
     * Specify 'media' to use the <code>specialpages-group-media</code> system interface 
     * message, which translates to 'Media reports and uploads' in English;
     * 
     * @return string
     */
    function getGroupName() {
			return 'contact';
    }
    
    function getContactFormMarkup(Helper $h)
    {
    	if($h)
    	{
    		return $h->render(array(
    			'title' => $this->formTitle,
    			'formType' => $this->formType,
    			'user'=>$this->user
    		));
    	}	
    	else return "<h2>Helper wasn't loaded!</h2>";
    }
}



interface Helper
{
	public function load();
}


class FileLoader implements Helper
{
	private $fileName;
	
	public function __construct($fileName)
	{
		$file = __DIR__."/$fileName";
		if(!file_exists($file))
		{
			throw new Exception("Could not find file: {$file}.");
		}
		$this->fileName = $file;
	}
	public function getFileName()
	{
		return $this->fileName;
	}
	public function load()
	{
		if(!file_exists($this->fileName))
		{
			return "<h2>Could not find file.</h2>";
		}
		return file_get_contents($this->fileName);
	}
	public function render($variables) {
		extract($variables, EXTR_SKIP); // Extract the variables to a local namespace
		ob_start();
		require $this->fileName; // Include the template file
		return ob_get_clean(); // End buffering and return its contents
	}
}