<?php
/**
 * MediaWiki Wikilog extension
 * Copyright © 2008-2010 Juliano F. Ravasi
 * http://www.mediawiki.org/wiki/Extension:Wikilog
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
 * @author Juliano F. Ravasi < dev juliano info >
 */

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'WikilogOcdla',
	'version'        				=> '1.0.0.0',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);


define('WIKILOG_SETTINGS_OCDLA',true);
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['WikilogOcdla'] = $dir . 'WikilogOcdla.i18n.php';
/**
 * Autoloaded classes:
 *	+ replace several classes previously defined by the Wikilog extension.
 *	+ In particular this lets us replace some functionality in the WikilogItemPager
 *	+ class that needs updating in order to support various interpolations for the "continue
 *	+ reading" text.
 */
$overrides = array(
	// WikilogOcdlaItemPager.php
	'WikilogItemPager'          => $dir . 'WikilogOcdlaItemPager.php',
	'WikilogSummaryPager'       => $dir . 'WikilogOcdlaItemPager.php',
	'WikilogTemplatePager'      => $dir . 'WikilogOcdlaItemPager.php',
	'WikilogArchivesPager'      => $dir . 'WikilogOcdlaItemPager.php',
	
	'WikilogPagerText'          => $dir . 'WikilogPagerText.php',
);

$wgAutoloadClasses = array_merge($wgAutoloadClasses,$overrides);