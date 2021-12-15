<?php
/**
 * MediaWiki AuthOcdla extension
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * @file
 * @ingroup Extensions
 * @author José Bernal
 */

if (!defined('MEDIAWIKI'))
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'AuthOcdla',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);



/**
 * Setup function--call this from LocalSettings.php
 *
 * Args:
 *  global $wgAuthOcdla_ReplaceLogin, set in LocalSettings.php
 */
final class AuthOcdlaSetup
{
	static $dir;

	/**
	 * Override classes.
	 *
	 * We override some local classes in the MediaWiki 
	 * installation, especially the SpecialUserlogin.php.
	 * This helps us troubleshoot bugs in the SpecialUserlogin file
	 * without altering core MW code.
	 */
	public static function OverrideMWLocalClass($className,$file)
	{
		global $wgAutoloadLocalClasses;
		if(!isset($wgAutoloadLocalClasses[$className]))
		{
			throw new Exception("The class, {$className}, could not be found in \$wgAutoloadLocalClasses.");
		}
		$wgAutoloadLocalClasses[$className] = self::$dir .'/classes/'.$file .'.php';
	}
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		global $wgAuthOcdla_ReplaceLogin;
		/**
		 * http://www.mediawiki.org/wiki/Manual:$wgRequest
		 */
		// $request = $out->getRequest();
		if($wgAuthOcdla_ReplaceLogin) {
			$out->addModules('ext.authOcdla');
		}
		if(true) {
			// $out->prependHTML('<img src="/extensions/AuthOcdla/images/login-image.png" />');
			// $out->mBodytext = '<h1>Foobar</h1>';
		}
		
		return true;
	}
	
	public static function SetupAuthOcdla()
	{
		global $wgAuthOcdla_ReplaceLogin,
	
		$wgResourceModules,
	
		$wgAuthOcdla_DisableSpecialSearchGo,
	
		$wgAuthOcdla_AddAuthData,
	
		$wgAutoloadClasses,
	
		$wgHooks;
	
		self::$dir = dirname(__FILE__);
	
	
	
		$wgResourceModules['ext.authOcdla'] = array(
			// JavaScript and CSS styles. To combine multiple files, just list them as an array.
			'scripts' => array( 'authocdla.js' ),
			// 'styles' => 'css/ext.myExtension.css',
	
			// When your module is loaded, these messages will be available through mw.msg().
			// E.g. in JavaScript you can access them with mw.message( 'myextension-hello-world' ).text()
			// 'messages' => array( 'myextension-hello-world', 'myextension-goodbye-world' ),
	
			// If your scripts need code from other modules, list their identifiers as dependencies
			// and ResourceLoader will make sure they're loaded before you.
			// You don't need to manually list 'mediawiki' or 'jquery', which are always loaded.
			// 'dependencies' => array( 'skins.lod.js' ),
	
			// You need to declare the base path of the file paths in 'scripts' and 'styles'
			// 'localBasePath' => __DIR__,
	
			// ... and the base from the browser as well. For extensions this is made easy,
			// you can use the 'remoteExtPath' property to declare it relative to where the wiki
			// has $wgExtensionAssetsPath configured:
			// 'remoteExtPath' => 'AuthOcdla',
	
			'position' => 'bottom',
			'remoteBasePath' => '/extensions/AuthOcdla',
			'localBasePath' => '/extensions/AuthOcdla'
		);
	
	
		$wgHooks['BeforePageDisplay'][] = 'AuthOcdlaSetup::onBeforePageDisplay';
	
		/**
		 * Class autoloader.
		 *
		 * Hook into MediaWiki's autoloader by telling it
		 * where to find the AuthOcdla and AuthOcdlaHooks classes.
		 */
		$wgAutoloadClasses['AuthOcdla'] = self::$dir . '/AuthOcdla.classes.php';
		$wgAutoloadClasses['AuthOcdlaHooks'] = self::$dir . '/AuthOcdla.hooks.php';
		$wgAutoloadClasses['AuthOcdlaPersonalLinks'] = self::$dir . '/classes/AuthOcdlaPersonalLinks.php';
		$wgAutoloadClasses['AuthOcdlaPersonalLinksManager'] = self::$dir . '/classes/AuthOcdlaPersonalLinksManager.php';
		$wgAutoloadClasses['AuthOcdlaPersonalLinksAnonymous'] = self::$dir . '/classes/AuthOcdlaPersonalLinksAnonymous.php';
		$wgAutoloadClasses['AuthOcdlaPersonalLinksAuthenticated'] = self::$dir . '/classes/AuthOcdlaPersonalLinksAuthenticated.php';
		$wgAutoloadClasses['DatabaseOcdlasession'] = self::$dir . '/classes/DatabaseOcdlasession.php';
	
		/**
		 * Load user data.
		 *
		 * Load additional data, especially IdP data for our user.
		 * The idea here is that we can load arbitrary profile data 
		 * from the Salesforce IdP or any other number of data sources for this user.
		 * and then use it to give that user a better browsing experience.
		 */
		$wgHooks['UserLoadFromSession'][] = 'AuthOcdlaHooks::onUserLoadFromSession';
	
		/**
		 * @jbernal
		 *
		 * @date 2017-07-26
		 *
		 * Not sure what the PersonalUrls hook does but it's currently throwing an error.
		 */
		if(false && $wgAuthOcdla_ReplaceLogin)
		{
			//Disallow logout link 
			$wgHooks['PersonalUrls'][] = 'Auth_ocdla_logoutlink_hook';
			//Hook to replace login link  
			$wgHooks['PersonalUrls'][] = 'Auth_ocdla_loginlink_hook'; 
		}
		if($wgAuthOcdla_DisableSpecialSearchGo)
		{
			$wgHooks['SpecialSearchGo'][] = 'AuthOcdlaHooks::onSpecialSearchGo';
		}
	}
}