<?php
header('Content-Type: application/json');

	 					
$logoutUrl = "https://auth.ocdla.org/logout?ref={$requestUrl}&server=".
$wgAuthOcdla_HostRedirect;

$data = array(
	'logged_in' => true,
	'markup' => '<a href="#" id="user-info-toggle"><img src="/skins/ocdla/images/login-image.png" /></a>
	<ul>
		<li id="pt-userpage">
			<a accesskey="." title="Your user page [.]" href="/User:'.$mName .'">
				'.$mName .'
			</a>
		</li>
		<li id="pt-salesforce">
			<a accesskey="." title="Salesforce portal [.]" target="_blank" href="//auth20150528--ocdladev.cs17.my.salesforce.com">Salesforce</a>
		</li>
		<li id="pt-ocdla">
			<a accesskey="." title="OCDLA Homepage [.]" target="_new" href="//www.ocdla.org">OCDLA</a>
		</li>
		<li id="pt-mytalk">
			<a accesskey="n" title="Your talk page [n]" class="new" href="/User_talk:'.$mName .'">My talk</a>
		</li>
		<li id="pt-preferences">
			<a title="Your preferences" href="/Special:Preferences">My preferences</a>
		</li>');

if(true || $mId == 3 || $mId == 25060) {
	$data['markup'] .= '
		<li id="pt-wikilog-blog">
			<a title="Add a Blog Post" href="/index.php?title=Blog:Main&action=wikilog">Add Blog Post</a>
		</li>
		<li id="pt-wikilog-case-review">
			<a title="Add a Case Review" href="/index.php?title=Blog:Case_Reviews&action=wikilog">Add Case Review</a>
		</li>
';
}

if($mId == 3 || $mId == 25060) {
	$data['markup'] .= '
		<li id="pt-emailer-review">
			<a title="Review new posts." href="/ocdlaemail/publish.php">Emailer Review</a>
		</li>
		<li id="pt-emailer-action">
			<a title="Notify subscribers of new posts." href="/ocdlaemail/sendmail.php">Send Email</a>
		</li>
';
}

if($legComm) {
	$data['markup'] .= '
		<li id="pt-legcomm">
			<a title="Legislative Affiars Committee" href="/Legcomm:Home">Legislative Affairs</a>
		</li>
';
}

if($sysOp) {
	$data['markup'] .= '
		<li id="pt-manage-groups">
			<a title="Manage Groups" href="/Special:UserRights/'.$mName.'">Manage Groups</a>
		</li>
';
}

$data['markup'] .= '
		<li id="pt-watchlist">
			<a accesskey="l" title="A list of pages you are monitoring for changes [l]" href="/Special:Watchlist">My watchlist</a>
		</li>
		<li id="pt-mycontris">
			<a accesskey="y" title="A list of your contributions [y]" href="/Special:Contributions/'.$mName .'>My contributions</a>
		</li>
		<li id="pt-logout">
			<a title="Log out" href="'.$logoutUrl . '">Log out</a>
		</li>	
</ul>';
print json_encode($data); exit;
?>
<!--
	<a href="/index.php?title=Special:UserLogout&amp;returnto=Blog%3AMain%2FSmall+coincidences%3A+Man+vs.+Corpse&amp;returntoquery=foo%3Dbar">Log out</a>
-->