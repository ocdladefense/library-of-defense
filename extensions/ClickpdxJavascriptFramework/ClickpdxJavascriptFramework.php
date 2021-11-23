<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'ClickpdxJavascriptFramework',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);



$dir = dirname( __FILE__ );
// $wgExtensionMessagesFiles['WikilogOcdla'] = $dir . 'WikilogOcdla.i18n.php';



class ClickpdxJavascriptFramework {


	public static function SetupClickpdxJavascriptFramework(){
		global $wgHooks, $wgResourceModules;

		$wgResourceModules['clickpdx.framework.js'] = array(
			'scripts' => array(
				'js/app-core.js',
				'js/controller.class.js',
				'js/view.class.js'
			),
			//'dependencies' => array(
				// In this example, awesome.js needs the jQuery UI dialog stuff
			//	'jquery.ui.dialog',
			//),
			'position' => 'top',
			'remoteBasePath' => '/extensions/ClickpdxJavascriptFramework',
			'localBasePath' => 'extensions/ClickpdxJavascriptFramework'
		);
	}
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		// if(in_array(strtolower($out->getPageTitle()),array('search results','search'))) {
			// $out->addModules('search.booksonline.js');
		// }
		
		return true;
	}

}