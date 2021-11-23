<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'PwaOcdla',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);

// $wgExtensionMessagesFiles['WikilogOcdla'] = $dir . 'WikilogOcdla.i18n.php';

$dir = dirname( __FILE__ );

class PwaOcdla {


	public static function SetupPwaOcdla(){
		global $wgHooks, $wgResourceModules;
		
		
		
		// $wgHooks['SpecialSearchCreateLink'][] = 'SetupBooksOnlineOcdla::onSpecialSearchCreateLink';
		$wgHooks['BeforePageDisplay'][] = 'PwaOcdla::onBeforePageDisplay';
		// $wgHooks['ParserFirstCallInit'][] = 'BooksOnlineOcdla::onParserSetup';

		// $wgResourceModules['ext.pwaOcdla.main.js'] = array(
		$wgResourceModules['ext.pwaOcdla.main'] = array(
			'scripts' => array(
				'/main.js'
			),
			'dependencies' => array(
				// In this example, awesome.js needs the jQuery UI dialog stuff
				// 'clickpdx.framework.js',
			),
			'position' => 'bottom',
			'remoteBasePath' => '/extensions/PwaOcdla',
			'localBasePath' => 'extensions/PwaOcdla'
		);
		
	}
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		global $wgHooks, $wgResourceModules;
// print "<pre>".print_r($wgResourceModules,true)."</pre>";exit;
		$out->addModules('ext.pwaOcdla.main');
		
		return true;
	}
	

}