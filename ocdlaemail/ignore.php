<?php
include('framework.php');
ob_end_clean();
include('feed_reader.php');
require_once("finditem.php");

$rows = sel("SELECT * FROM wikilog_email_records")->startWhere()->dieEq("guid",$guid)->execute();
if(count($rows) < 1){
	echo "Ignored:".$title;
		$op = new operation();
		$op->setTable("wikilog_email_records");
		$op->addItems(array("title" => $title, "guid" => $guid));
		$op->insert();
	} else {
	echo "This entry has already been sent.";
}
?>