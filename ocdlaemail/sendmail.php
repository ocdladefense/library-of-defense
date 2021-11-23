<?php
include('framework.php');
ob_end_clean();

require_once("finditem.php");
require_once("mailer.php");

// Check Row
$rows = sel("SELECT * FROM wikilog_email_records")->startWhere()->dieEq("guid",$guid)->execute();

if(count($rows) > 0){
	echo "This entry has already been sent.";
	exit;
}


$emails = sel("select email from wikilog_email_subscriptions where activated = 1")->executeSingleColumn();
if(count($emails) < 1){
	print("no subscribers");
	exit;
}
foreach($emails as $email){
	// If row empty send email and happy blogging
	$unsubscribeLink = "https://libraryofdefense.ocdla.org/ocdlaemail/unsubscribe.php?email=" . $email;
	$body = $desc."<br /><br />Full article: <a href=\"".$link."\" rel=\"nofollow\">" . $link . "</a>";
	$body = $body . "<br /><br />Unsubscribe: <a href=\"" . $unsubscribeLink . "\">" . $unsubscribeLink . "</a>";
	send_mail($body, "Library of Defense: " . $title, $email);
}
$op = new operation();
$op->setTable("wikilog_email_records");
$op->addItems(array("title" => $title, "guid" => $guid));
$op->insert();
echo "Sent: ".$title;
?>