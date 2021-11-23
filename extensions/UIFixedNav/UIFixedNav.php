<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'UIFixedNav',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);

// $wgExtensionMessagesFiles['WikilogOcdla'] = $dir . 'WikilogOcdla.i18n.php';

$dir = dirname( __FILE__ );

class UIFixedNav {

	// const DRAWER_TEMPLATE = 'bon-drawer.html';
	const DRAWER_TEMPLATE = 'fixed-nav.html';



	public static function SetupUIFixedNav(){
		global $wgHooks, $wgResourceModules, $wgOcdlaShowBooksOnlineDrawer;
		
		
		$wgHooks['BeforePageDisplay'][] = 'UIFixedNav::onBeforePageDisplay';

		
		$wgResourceModules['ext.uiFixedNav'] = array(
			'styles' => array(
				'css/fixed-nav.css',
			),
			'position' => 'top',
			'remoteBasePath' => '/extensions/UIFixedNav',
			'localBasePath' => 'extensions/UIFixedNav'
		);
	}
	
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		global $wgOcdlaShowBooksOnlineDrawer, $wgOcdlaShowBooksOnlineNs;
	
		$out->addModules('ext.uiFixedNav');
		/*
		$out->addModuleStyles( [
			'ext.uiDrawer'
		] );
		*/
		if(is_array($skin->customElements)){
			$skin->customElements = array(
				'fixedNav' => file_get_contents(__DIR__.'/templates/'.self::DRAWER_TEMPLATE)
			);
		} else {
			$skin->customElements = array(
				'fixedNav' => file_get_contents(__DIR__.'/templates/'.self::DRAWER_TEMPLATE)
			);
		}



		return true;
	}

}