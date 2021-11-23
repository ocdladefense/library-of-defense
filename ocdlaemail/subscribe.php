<?php
require("mailer.php");
require("framework.php");
function body_content(){
	if(isset($_REQUEST["email"]) && strlen($_REQUEST["email"]) > 3) {
		$email = $_REQUEST["email"];

		$exists = sel("select guid, email, activated from wikilog_email_subscriptions where")->dieEq("email", $email)->execute();

		if(count($exists) > 0 && $exists[0]['activated'] == 1){
			?>Looks like that address is already subscribed. Did you want to unsubscribe?<br/><br/>
			<a href="unsubscribe.php?email=<?php print($email); ?>">unsubscribe</a><br/>
			<?php
			exit;
		} else if(count($exists) < 1){
			$guid = md5($email . $email . $email . time());
			$op = new operation();
			$op->setTable("wikilog_email_subscriptions");
			$op->addItem("email", $email);
			$op->addItem("guid", $guid);
			$op->insert();
		} else {
			$guid = $exists[0]['guid'];
		}

		$body = "Please go to this address to confirm your subscription to the OCDLA blog.<br/><br/>\n";
		$authorizeUrl = "http://libraryofdefense.ocdla.org/ocdlaemail/authorize.php?guid=" . $guid;
		$body .= "<a href=\"" . $authorizeUrl . "\">" . $authorizeUrl . "</a>";
		send_mail($body, "Confirm your subscription to the OCDLA blog", $email);

		?>
		<div class="container">
			<br/><br/>
		<div class="row">
			<span class="span8">
			We've sent an email to <?php print($email); ?>
			with an activation link. Click on it to finish the subscription process, and thanks for signing up for the OCDLA Blog email.
			<br/><br/>
			<a href="/">Back to the Library of Defense</a>.
			</span>
		</div>
		</div>
		<?php
	} else {
		?>Did you want to subscribe to the OCDLA Library of Defense Blog?<br/>
		<form action="subscribe.php">
			Email: <input type="text" name="email">
			<input type="submit" name="submit" value="Subscribe">
		</form>
		<?php
	}
}