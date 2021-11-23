<?php
require_once("class.phpmailer.php");
function send_mail($body, $subject, $toAddress)
{
	$mail = new PHPMailer();
	$mail->IsSMTP();                                      // set mailer to use SMTP
	$mail->Host = "mail.pacinfo.com";  // specify main and backup server
	$mail->Port = 587;
	//$mail->SMTPSecure = "ssl";
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = "account@ocdla.org";  // SMTP username
	$mail->Password = "Defend2Mail"; // SMTP password

	$mail->From = "account@ocdla.org";
	$mail->FromName = "Library of Defense";
	$mail->AddAddress($toAddress);

	$mail->IsHTML(true);                               // set email format to HTML
	$mail->SMTPDebug = 1;

	$mail->Subject = $subject;
	$mail->Body    = $body;
	$mail->AltBody = strip_tags($body);
	$ret = $mail->Send();
	if(!$ret){
	   echo "Message could not be sent: " . $toAddress . "\n\n";	   
	   echo "Mailer Error: " . $mail->ErrorInfo;	   
	}
}