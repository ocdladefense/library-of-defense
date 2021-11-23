<?php

/**
 * Ocdla skin
 *
 * @file
 * @ingroup Skins
 * @author Jose Bernal (http://clickpdx.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */


// Continued from above
$wgValidSkinNames['ocdla'] = 'Ocdla';
$wgExtensionMessagesFiles['ocdla'] = __DIR__ . '/Ocdla.i18n.php';
$wgAutoloadClasses['SkinOcdla'] = __DIR__ . '/Ocdla.php';
$wgMessagesDirs['Ocdla'] = __DIR__ . '/i18n';


// Continued from above
/*
 * Template for reference.
 
$wgResourceModules['skins.ocdla'] = array(
	'styles' => array(
		'ocdla/resources/screen.css' => array( 'media' => 'screen' ),
		'ocdla/resources/print.css' => array( 'media' => 'print' ),
	),
	'remoteBasePath' => $GLOBALS['wgStylePath'],
	'localBasePath' => $GLOBALS['wgStyleDirectory'],
	'position' => 'top'
);
*/
  
$wgResourceModules['skins.ocdla'] = array(
	// Keep in sync with WebInstallerOutput::getCSS()
	'styles' => array(
		'ocdla/resources/screen.css' => array( 'media' => 'screen' ),
		'ocdla/resources/overrides.css' => array( 'media' => 'screen' ),
		'ocdla/resources/screen-hd.css' => array( 'media' => 'screen and (min-width: 982px)' ),
		'ocdla/css/drawer.css'  => array( 'media' => 'screen' ),
		'ocdla/css/books-online.css'  => array( 'media' => 'screen' )
	),
	'position' => 'top',
	'remoteBasePath' => 'skins',
	'localBasePath' => 'skins'
);


$wgResourceModules['skins.ocdla.js'] = array(
	'scripts' => array(
		'ocdla/js/app-core.js',
		'ocdla/resources/ocdla.js',
		'ocdla/js/mark.min.js',
		'ocdla/resources/lunr.js',
		'ocdla/resources/books.js',
		'ocdla/resources/view.class.js',
		'ocdla/resources/lunr-indexer.js',
		'ocdla/resources/books-online.js',
		'ocdla/resources/books-online-view.js',
		'ocdla/js/books-online-loader.js'
	),
	'dependencies' => array(
		// In this example, awesome.js needs the jQuery UI dialog stuff
		'jquery.makeCollapsible',
		'jquery.ui.accordion',
		'clickpdx.framework.js'
	),
	'position' => 'top',
	'remoteBasePath' => 'skins',
	'localBasePath' => 'skins'
);