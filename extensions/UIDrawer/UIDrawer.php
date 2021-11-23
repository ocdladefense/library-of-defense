<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'UIDrawer',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);

// $wgExtensionMessagesFiles['WikilogOcdla'] = $dir . 'WikilogOcdla.i18n.php';

$dir = dirname( __FILE__ );

class UIDrawer {

	const DRAWER_TEMPLATE = 'drawer.html';


	public static function SetupUIDrawer(){
		global $wgHooks, $wgResourceModules, $wgOcdlaShowBooksOnlineDrawer;
		
		
		$wgHooks['BeforePageDisplay'][] = 'UIDrawer::onBeforePageDisplay';

		
		$wgResourceModules['ext.uiDrawer'] = array(
			'styles' => array(
				'css/drawer.css',
				'css/accordion.css'
			),
			'position' => 'top',
			'remoteBasePath' => '/extensions/UIDrawer',
			'localBasePath' => 'extensions/UIDrawer'
		);
	}
	
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		global $wgOcdlaShowBooksOnlineDrawer, $wgOcdlaShowBooksOnlineNs;
	
		$out->addModules('ext.uiDrawer');
		/*
		$out->addModuleStyles( [
			'ext.uiDrawer'
		] );
		*/
		$skin->customElements += array(
			'drawer' => file_get_contents(__DIR__.'/templates/'.self::DRAWER_TEMPLATE)
		);

		return true;
	}

}