<?php
namespace Messaging\Mail;

class MailMessage {


	protected $recipients;
	
	protected $from;
	
	protected $subject;
	
	protected $headers;
	
	protected $additionalParameters;
	
	protected $htmlBody;
	
	protected $textBody;
	
	protected $boundary; 
	
	protected $multipart = false;

	public function __construct()
	{
		$this->headers = array(
		 'MIME-Version' => '1.0',
		 'Content-Transfer-Encoding' => '8Bit'
		);
	}
	
	public function multipart($boolean)
	{
		if(isset($boolean)&&!$boolean)
		{
			$this->multipart = $boolean;
		}
		else if(isset($boolean)&&$boolean)
		{
			$this->multipart = $boolean;
			// $this->boundary = '----=_NextPart_';
			$this->boundary = '_NextPart_';
		 	$this->addMailHeader(
		 		'Content-Type',
		 		'multipart/alternative; boundary="'.$this->boundary.'"'
		 	);
		}
		return $this->multipart;
	}
	protected function getMailHeaders()
	{
		return $this->headers;
	}
	protected function formatMailHeaders(){
		$out = '';
		foreach($this->getMailHeaders() as $header=>$value)
		{
			$out .= $header.':'.$value ."\r\n";
		}
		return $out;
	}
	public function textBody($str)
	{
		$this->textBody = $str;	
	}
	public function htmlBody($str)
	{
		$this->htmlBody = $str;
	}
	protected function addMailHeader($name,$value)
	{
		if(!is_array($name))
		{
			$arr = array($name=>$value);
			$this->headers=array_merge($this->headers,$arr);
		}
	}
	protected function getMultiBody()
	{
$multi_body="

This is a multi-part message in MIME format.

--{$this->boundary}
Content-Type: text/plain; charset=UTF-8; format=flowed;

{$this->textBody}


--{$this->boundary}
Content-Type: text/html; charset=UTF-8; format=flowed;

<html>
<head>
<title>Head Start of Lane County</title>
</head>
<body>
{$this->htmlBody}
</body>
</html>
";
	return $multi_body;
	}
	protected function getMultiBody2()
	{
$multi_body="

This is a multi-part message in MIME format.

--{$this->boundary}
Content-Type: text/plain; charset=UTF-8; format=flowed; 
Content-Transfer-Encoding: 8bit

{$this->textBody}


--{$this->boundary}
Content-Type: text/html; charset=UTF-8; format=flowed; 
Content-Transfer-Encoding: 8bit
<html>
<head>
<title>Head Start of Lane County</title>
</head>
<body>
{$this->htmlBody}
</body>
</html>
";
	return $multi_body;
	}
	public function addParameter($paramString)
	{
		$this->additionalParameters[]=$paramString;
	}
	protected function hasAdditionalParameters()
	{
		return count($this->additionalParameters);
	}	
	protected function formatAdditionalParameters()
	{
		return implode(';',$this->additionalParameters);
	}
	protected function sendWithValues($to,$subject,$body)
	{
		if($this->hasAdditionalParameters())
		{
			return mail($to,$subject,$body,$this->formatMailHeaders(),$this->formatAdditionalParameters());
		}
		return mail($to,$subject,$body,$this->formatMailHeaders());
	}
	protected function send()
	{
		return mail('jbernal.web.dev@gmail.com','HSOLC Announcements', $this->getMultiBody(), $this->formatHeaders());
	}
	
}