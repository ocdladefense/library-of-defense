<?php include("framework.php");

function body_content(){

	if(isset($_REQUEST["email"])) {
		$email = $_REQUEST["email"];
		$op = new operation();
		$op->setTable("wikilog_email_subscriptions");
		$op->dieEq("email", $email);
		$op->delete();
		?>Sorry to see you go. We've removed you from our lists.
		You are always welcome to sign up again at the main <a href="/">Library of Defense</a> page. <?php
	} else {
		?>Did you want to unsubscribe?<br/>
		<form action="unsubscribe.php">
			Email: <input type="text" name="email">
			<input type="submit" name="submit" value="Unsubscribe">
		</form>
		<?php
	}
}