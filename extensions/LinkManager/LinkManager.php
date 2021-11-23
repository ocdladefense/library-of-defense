<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'LinkManager',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);

// $wgExtensionMessagesFiles['LinkManager'] = $dir . 'LinkManager.i18n.php';

$dir = dirname( __FILE__ );

class LinkManager {

	public static function SetupLinkManager(){
		global $wgHooks, $wgResourceModules, $wgLinkManagerEnabledModules;
		
		// $wgHooks['SpecialSearchCreateLink'][] = 'SetupBooksOnlineOcdla::onSpecialSearchCreateLink';
		$wgHooks['BeforePageDisplay'][] = 'LinkManager::onBeforePageDisplay';
		
		/*
				$wgResourceModules['ext.booksOnline.webapp.js'] = array(
					'scripts' => array(
						'js/search.controller.js'
					),
					'dependencies' => array(
						// In this example, awesome.js needs the jQuery UI dialog stuff
						'clickpdx.framework.js',
					),
					'position' => 'bottom',
					'remoteBasePath' => '/extensions/BooksOnlineOcdla',
					'localBasePath' => 'extensions/BooksOnlineOcdla'
				);
		*/
		$wgResourceModules['ext.linkManager.modules'] = array(
			'styles' => array(),
			'scripts' => array(),//'modules/ojd.js'),
			'position' => 'bottom',
			'remoteBasePath' => '/extensions/LinkManager',
			'localBasePath' => 'extensions/LinkManager'
		);

		$wgResourceModules['ext.linkManager.manager'] = array(
			'styles' => array(),
			'scripts' => array('js/link-manager.js'),
			'dependencies' => array('ext.linkManager.modules'),
			'position' => 'bottom',
			'remoteBasePath' => '/extensions/LinkManager',
			'localBasePath' => 'extensions/LinkManager'
		);
		
		$wgResourceModules['ext.linkManager.app'] = array(
			'styles' => array(),
			'scripts' => array('js/config.js'),
			'dependencies' => array('ext.linkManager.manager'),
			'position' => 'top',
			'remoteBasePath' => '/extensions/LinkManager',
			'localBasePath' => 'extensions/LinkManager'
		);
		
		foreach($wgLinkManagerEnabledModules as $module){
			$wgResourceModules['ext.linkManager.modules']['scripts'][] = 'modules/'.$module.'.js';
		}
	}
	
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		// $out->addModules('ext.linkManager.modules');
		$out->addModules('ext.linkManager.app');
		
		/*$out->addHeadItem( 'linkManager',
			'<script type="text/javascript">
				var linkHandlers
				mw.loader.using(\'ext.linkManager.manager\' )
				.then(function() {
					window.alert(\'foobar\');
					LINK_MANAGER.addRegisteredHandler(\'OJD\');
				});
			
			</script>'
		);
		*/
		return true;
	}


	/**
	 * @unused
	 *
	 * @description,
	 *  Can be used to only place this extension on certain namspaces
	 *  or pages.
	 *
	 */
	public static function checkNamespace(OutputPage &$out, Skin &$skin){
		global $wgOcdlaShowBooksOnlineDrawer, $wgOcdlaShowBooksOnlineNs;
		
		$checkNamespace = isset($wgOcdlaShowBooksOnlineNs) && $wgOcdlaShowBooksOnlineNs != NS_ALL;
		
		$skin->customElements = array();
		
		$title = $out->getTitle();
		$ns = $title->getNamespace();
		

		//if(in_array(strtolower($out->getPageTitle()),array('search results','search'))) {
		if($wgOcdlaShowBooksOnlineDrawer)
		{
			if($checkNamespace && !in_array($ns,$wgOcdlaShowBooksOnlineNs,true))
			{
				return true;
			}
			
			$out->addModules('ext.booksOnline.drawer');
			$skin->customElements = array(
				'drawer' => file_get_contents(__DIR__.'/templates/'.self::DRAWER_TEMPLATE)
			);
		}
		
		return true;
	}

}