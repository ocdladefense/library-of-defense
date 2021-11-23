<?php

/* These items don't work because MediaWiki must strip the headers...*/
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$redirect = 'https://auth.ocdla.org'.$wgAuthOcdlaStartPage.'?retURL='.urlencode($wgAuthOcdla_HostRedirect .$requestUrl);

$data = array(
	'logged_in' => false,
	'markup' => '<div class="" id="p-personal">
	<a href="#" id="user-info-toggle"><img src="/skins/ocdla/images/login-image.png" /></a>
	<ul>
		<li id="pt-login"><a accesskey="o" title="You are encouraged to log in; however, it is not mandatory [ctrl-option-o]" href="'.$redirect.'">OCDLA Single Sign-On</a></li>
	</ul>
</div>');
print json_encode($data); exit;