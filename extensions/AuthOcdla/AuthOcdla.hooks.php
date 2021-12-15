<?php

class AuthOcdlaHooks
{
	public static function onSpecialSearchGo(&$title)
	{
		$title = null;
		return true;
	}
	
	public static function onUserLoadFromSession($user, &$result)
	{
		$user->samlUid = 'foo';
		$user->samlUsername = 'uname';
		return true;
	}
	
	/**
	 * Hook function to rewrite login link to point to the OCDLA login page
	 */
	function Auth_ocdla_loginlink_hook(&$personal_urls, &$title)
	{

		// http://www.mediawiki.org/wiki/Manual:$wgRequest
		global $wgRequest;
		$referral = $wgRequest->getFullRequestURL();
		print $referral;exit;
		$appendQueryString = '?ref='.$referral;

		/*$personal_urls['login'] = array (
		'text' => wfMsg('userlogin'	),
		'href' => $GLOBALS['wgAuthOcdla_LoginURL'] . $appendQueryString );

		$personal_urls['anonlogin'] = array(
			'text' => NULL,
		);
		*/



	     



		if ($GLOBALS['wgShowIPinHeader'] && isset($_COOKIE[ini_get("session.name")]))
		{
			// shown for anonymous users without a session?
			$personal_urls['anonlogin'] = array (
				'text' => wfMsg( 'userlogin'	),
				'href' => $GLOBALS['wgAuthOcdla_LoginURL'] . $appendQueryString);
		}
		else
		{
			//$wgUser = 
			// shown for anonymous users with a session?
			$personal_urls['login'] = array(
				'text' => wfMsg('userlogin'),
				'href' => $GLOBALS['wgAuthOcdla_LoginURL'] . $appendQueryString
			);
		}






		return true;
	}
}