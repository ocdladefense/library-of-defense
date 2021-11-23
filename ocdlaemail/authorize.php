<?php include("framework.php");

function body_content(){

	if(isset($_REQUEST["guid"])) {
		$guid = $_REQUEST["guid"];	
		$q = sel("select email from wikilog_email_subscriptions where");
		$q->dieEq("guid", $guid);
		$rows = $q->execute();
		if($rows && count($rows) > 0){		
			$op = new operation();
			$op->setTable("wikilog_email_subscriptions");
			$op->dieEq("guid", $guid);
			$op->addItem("activated", 1);
			$num = $op->update();
			?>You're now subscribed!<br/><?php
		} else {
			?>It looks like that link has expired. Try <a href="subscribe.php">signing up</a> again. <br/><?php
		}
	} else {
		?>Not sure what's going on here, but we're missing your subscription ID.<br/><?php
	}

	?><a href="/">Library of Defense</a><?php
}