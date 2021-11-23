<?php


/**
 * This is the main web entry point for MediaWiki.
 *
 * If you are reading this in your web browser, your server is probably
 * not configured correctly to run PHP applications!
 *
 * See the README, INSTALL, and UPGRADE files for basic setup instructions
 * and pointers to the online documentation.
 *
 * http://www.mediawiki.org/
 *
 * ----------
 *
 * Copyright (C) 2001-2011 Magnus Manske, Brion Vibber, Lee Daniel Crocker,
 * Tim Starling, Erik Möller, Gabriel Wicke, Ævar Arnfjörð Bjarmason,
 * Niklas Laxström, Domas Mituzas, Rob Church, Yuri Astrakhan, Aryeh Gregor,
 * Aaron Schulz, Andrew Garrett, Raimond Spekking, Alexandre Emsenhuber
 * Siebrand Mazeland, Chad Horohoe, Roan Kattouw and others.
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
 *
 * @file
 */
# Uncomment this section to display errors
# Useful for debugging
/*
ini_set("log_errors", 1);
ini_set("error_log", "/home/users/lod/lodtest/html/errorlog.txt");

// http://www.php.net/manual/en/errorfunc.configuration.php#ini.display-errors
ini_set('display_errors','1');
error_reporting( E_ALL ^ E_NOTICE );
*/
# Bail on old versions of PHP.  Pretty much every other file in the codebase
# has structures (try/catch, foo()->bar(), etc etc) which throw parse errors in
# PHP 4. Setup.php and ObjectCache.php have structures invalid in PHP 5.0 and
# 5.1, respectively

if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.3.2' ) < 0 ) {
	// We need to use dirname( __FILE__ ) here cause __DIR__ is PHP5.3+
	require( dirname( __FILE__ ) . '/includes/PHPVersionError.php' );
	wfPHPVersionError( 'index.php' );
}

$foo  = ini_set('session.gc_maxlifetime', 2592000);
$bar  = ini_set('session.cookie_lifetime', 2592000);

# Initialise common code.  This gives us access to GlobalFunctions, the
# AutoLoader, and the globals $wgRequest, $wgOut, $wgUser, $wgLang and
# $wgContLang, amongst others; it does *not* load $wgTitle
if ( isset( $_SERVER['MW_COMPILED'] ) ) {
	require ( 'phase3/includes/WebStart.php' );
} else {
	require ( __DIR__ . '/includes/WebStart.php' );
}

$foo  = ini_set('session.gc_maxlifetime', 2592000);
$bar  = ini_set('session.cookie_lifetime', 2592000);
$mediaWiki = new MediaWiki();

$foo  = ini_set('session.gc_maxlifetime', 2592000);
$bar  = ini_set('session.cookie_lifetime', 2592000);
$mediaWiki->run();