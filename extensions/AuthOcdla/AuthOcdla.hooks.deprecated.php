<?php

class AuthOcdlaHooksDeprecated
{
	/**
	 * Hook function to rewrite login link to point to the OCDLA login page
	 */
	public static function Auth_ocdla_loginlink_hook( & $personal_urls, & $title ) {

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
	
		if ( $GLOBALS['wgShowIPinHeader'] && isset( $_COOKIE[ini_get( "session.name" )] ) ) {
			// shown for anonymous users without a session?
			$personal_urls['anonlogin'] = array (
				'text' => wfMsg( 'userlogin'	),
				'href' => $GLOBALS['wgAuthOcdla_LoginURL'] . $appendQueryString);
		} else {
			//$wgUser = 
			// shown for anonymous users with a session?
			$personal_urls['login'] = array (
				'text' => wfMsg('userlogin'	),
				'href' => $GLOBALS['wgAuthOcdla_LoginURL'] . $appendQueryString);
		}
	
		/*array (
		'text' => wfMsg( 'userlogin'	),
		'href' => $GLOBALS['wgAuthOcdla_LoginURL'] . $appendQueryString );
		*/

		 return TRUE;
	}


	/**
	 * Hook function to rewrite logout link to point to the OCDLA Login Page
	 */
	public static function Auth_ocdla_logoutlink_hook(&$personal_urls, &$title) {

		// http://www.mediawiki.org/wiki/Manual:$wgRequest
		// https://libraryofdefense.ocdla.org/index.php?title=Special%3AAllMessages&prefix=user&filter=all&lang=en&limit=500
		global $wgRequest;
		$referral = $wgRequest->getFullRequestURL();
		$appendQueryString = '?server=libraryofdefense.ocdla.org&ref=/Welcome_to_The_Library';

		$personal_urls['logout'] = array (
			'text' => wfMsg('userlogout' ),
			'href' => $GLOBALS['wgAuthOcdla_LogoutURL'] . $appendQueryString );

		return TRUE;
	}
}