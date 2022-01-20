<?php

require __DIR__ . '/vendor/autoload.php';

# See "DefaultSettings.php" for all possible settings, but only customize by setting in this file, or "SiteSpecificSettings.php".

# Protect against web entry
if(!defined('ALT_SCRIPT') && !defined('MEDIAWIKI')) exit;

require_once('SiteSpecificSettings.php');
require_once('SiteSpecificSettingsLogging.php');



# Disable editing; does not disable all database writes (e.g., increment view counter, etc.)
// $wgReadOnly = "We are upgrading MediaWiki, please be patient. This wiki will be back in a few hours.";

// http://www.mediawiki.org/wiki/Category:Search_variables
# $wgAdvancedSearchHighlighting = true;

## Uncomment this line to disable 'B' type password storage (the default)
## in favor of 'A' type password storage of standard md5 passwords
## see documentation at:
## http://www.mediawiki.org/wiki/Manual_talk:User_table
$wgPasswordSalt = false;

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = "OCDLA Library of Defense";
$wgMetaNamespace = "Ocdla";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs please see:
## http://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptExtension = ".php";

$wgArticlePath = "$wgScriptPath/$1";
$wgUsePathInfo = true;

## The protocol and server name to use in fully-qualified URLs
# Danger! $wgServer must include both a protocol and a domain,
# otherwise calls to api.php or load.php will be executed with
# malformed URLs.

// By default, use https, just in case this isn't properly set in SiteSpecificSettings.php.
$ocdlaProtocol = $ocdlaProtocol ?: 'https';

// Important setting used through MW requests, especially to construct other resource (images, js, css) URLs.
$wgServer = "{$ocdlaProtocol}://" . (empty($subdomain) ? $domain : ("$subdomain.$domain"));




# Cookie settings.
$wgCookieDomain = $subdomain . "." . $domain;

$wgCookiePrefix = $cookiePrefix;

/**
 * Set the default cookie expiration to 1 month
 * This ensures that users remain logged in for a reasonable
 * period of time before their cookie is invalidated.
 * Per OCDLA's request.
 */
$wgCookieExpiration = 30*86400;

$wgCookieSecure = true;

$wgCookieHttpOnly = false;

## The relative URL path to the skins directory
$wgStylePath = "$wgScriptPath/skins";

// $wgHandheldStyle='chick/main.css';
// $wgHandheldForIPhone=true;

## The relative URL path to the logo.  Make sure you change this from the default, or else you'll overwrite your logo when you upgrade!
$wgLogo = "$wgStylePath/common/images/wiki.png";

## UPO means: this is also a user preference option
$wgEnableEmail = false;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "apache@localhost";
$wgPasswordSender = "apache@localhost";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

# MySQL specific settings
# All table prefixes have been removed as of 2014-08-04
# $wgDBprefix         = "xxxxx_";









# MySQL table options to use during installation or update
$wgDBTableOptions  = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Experimental charset support for MySQL 4.1/5.0.
$wgDBmysql5 = true;

# Site language code, should be one of the list in ./languages/Names.php
$wgLanguageCode = "en";

$wgSecretKey = "e7afda73d8140af47a36ef195e230644e9e88a5e1de25e94150829c78c95a38e";

# Site upgrade key. Must be set to a string (default provided) to turn on the web installer while LocalSettings.php is in place
$wgUpgradeKey = "ac187d71b94fc363";


/**
 * MediaWiki relies on cache directives in LocalSettings.php
 * These cache settings determine whether MediaWiki uses the object cache
 * and what kinds of data are cached.
**/
## Shared memory settings
# http://www.mediawiki.org/wiki/Manual:$wgMainCacheType
$wgMainCacheType = CACHE_NONE; //CACHE_DB;

# http://www.mediawiki.org/wiki/Manual:$wgMemCachedServers
# $wgMemCachedServers = array();

# Added by Will Shaver to remove all caching in an attempt to fix front page bug
$wgEnableParserCache = false;

# http://www.mediawiki.org/wiki/Manual:$wgParserCacheType
$wgParserCacheType = CACHE_NONE; //isset($ocdlaParserCacheType) ? $ocdlaParserCacheType : CACHE_DB;

# http://www.mediawiki.org/wiki/Manual:$wgCachePages
$wgCachePages = false;

$wgVarnishHost = "207.189.130.196";


## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true: $wgEnableUploads = true;
# $wgUseImageMagick = true;
# $wgImageMagickConvertCommand 	= "/usr/bin/convert";

# InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.utf8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
# $wgHashedUploadDirectory = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
# $wgCacheDirectory = "$IP/cache";


$wgMaxTocLevel = 2;
$wgRestrictDisplayTitle = false;
$wgAllowDisplayTitle = true;


## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'standard', 'nostalgia', 'cologneblue', 'monobook', 'vector':
// mw.v 1.24.x or less

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl  = "";
$wgRightsText = "";
$wgRightsIcon = "";
# $wgRightsCode = ""; # Not yet used

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

# Query string length limit for ResourceLoader. You should only set this if
# your web server has a query string length limit (then set it to that limit),
# or if you have suhosin.get.max_value_length set in php.ini (then set it to
# that value)
$wgResourceLoaderMaxQueryLength = -1;







####################### PAGE AND NAMESPACE ACCESS CONFIGURATIONS #######################################################
########################################################################################################################

# MediaWiki default Namespace/Constants. 
/**
 *Value		Name					Constant
 *0			Main					NS_MAIN
 *1			Talk					NS_TALK
 *2			User					NS_USER
 *3			User talk				NS_USER_TALK
 *4			Project					NS_PROJECT
 *5			Project talk			NS_PROJECT_TALK
 *6			File					NS_FILE
 *7			File talk				NS_FILE_TALK
 *8			MediaWiki				NS_MEDIAWIKI
 *9			MediaWiki talk			NS_MEDIAWIKI_TALK
 *10		Template				NS_TEMPLATE
 *11		Template talk			NS_TEMPLATE_TALK
 *12		Help					NS_HELP
 *13		Help talk				NS_HELP_TALK
 *14		Category				NS_CATEGORY
 *15		Category talk			NS_CATEGORY_TALK
 *-1		Special					NS_SPECIAL
 *-2		Media					NS_MEDIA
 */


define("NS_BLOG", 100);
define("NS_BLOG_TALK", 101);

define("NS_BLOG_CASES",	102);
define("NS_BLOG_CASES_TALK", 103);

define("NS_LEGCOMM", 200);
define("NS_LEGCOMM_TALK", 201);

define("NS_PUBLIC", 400);
define("NS_PUBLIC_TALK", 401); 

define("NS_DTN", 500);
define("NS_DTN_TALK", 501);

define("NS_DNB", 502);
define("NS_DNB_TALK", 503);

define("NS_FSM", 504);
define("NS_FSM_TALK", 505);

define("NS_IM",	508);
define("NS_IM_TALK", 509);

define("NS_MH",	510);
define("NS_MH_TALK", 511);

define("NS_PJ",	512);
define("NS_PJ_TALK", 513);

define("NS_SE",	514);
define("NS_SE_TALK", 515);

define("NS_SSM", 516);
define("NS_SSM_TALK", 517);

define("NS_TNB", 518);
define("NS_TNB_TALK", 519);

define("NS_VET", 520);
define("NS_VET_TALK", 521);

define("NS_SSMUP",	522);
define("NS_SSMUP_TALK", 523);

define("NS_SEXCASES", 524);
define("NS_SEXCASES_TALK", 525);

define("NS_ALL",99999999);


$wgExtraNamespaces = array(
	
	NS_PUBLIC        => "Public",
	NS_PUBLIC_TALK	 => "Public_talk",

	NS_DTN			 => "DUII_Trial_Notebook",
	NS_DTN_TALK		 => "DUII_Trial_Notebook_talk",
	
	NS_DNB			 => "DUII_Notebook",
	NS_DNB_TALK		 => "DUII_Notebook_talk",
	
	NS_FSM			 => "Felony_Sentencing_in_Oregon",
	NS_FSM_TALK		 => "Felony_Sentencing_in_Oregon_talk",

	NS_IM			 => "Investigators_Manual",
	NS_IM_TALK		 => "Investigators_Manual_talk",
	
	NS_MH			 => "Mental_Health_Manual",
	NS_MH_TALK		 => "Mental_Health_Manual_talk",
	
	NS_PJ			 => "PostJudgment_Manual",
	NS_PJ_TALK		 => "PostJudgment_Manual_talk",
	
	NS_SE			 => "Scientific_Evidence_Manual",
	NS_SE_TALK		 => "Scientific_Evidence_Manual_talk",
	
	NS_SSM			 => "Search_and_Seizure",
	NS_SSM_TALK		 => "Search_and_Seizure_talk", 
	
	NS_SSMUP		 => "Search_and_Seizure_Updates",
	NS_SSMUP_TALK	 => "Search_and_Seizure_talk",  
	
	NS_TNB			 => "Trial_Notebook", 
	NS_TNB_TALK	     => "Trial_Notebook_talk",
	
	NS_SEXCASES		 => "Defending_Sex_Cases",
	NS_SEXCASES_TALK => "Defending_Sex_Cases_talk",
	
	NS_VET			 => "Veterans_Manual",
	NS_VET_TALK		 => "Veterans_Manual_talk"
);

# https://www.mediawiki.org/wiki/Manual:Using_custom_namespaces
$wgNamespaceAliases['DTN'] = NS_DTN;
$wgNamespaceAliases['DNB'] = NS_DNB;
$wgNamespaceAliases['FSM'] = NS_FSM;
$wgNamespaceAliases['IM'] = NS_IM;
$wgNamespaceAliases['MH'] = NS_MH;
$wgNamespaceAliases['PJ'] = NS_PJ;
$wgNamespaceAliases['SE'] = NS_SE;
$wgNamespaceAliases['SSM'] = NS_SSM;
$wgNamespaceAliases['SSMUP'] = NS_SSMUP;
$wgNamespaceAliases['TNB'] = NS_TNB;
$wgNamespaceAliases['SEXCASES'] = NS_SEXCASES;
$wgNamespaceAliases['VET'] = NS_VET;
  

$wgNamespacesToBeSearchedDefault = array(
	NS_MAIN       => true,
	NS_PUBLIC	  => true,
	NS_DTN		  => true,
	NS_DNB		  => true,
	NS_FSM		  => true,
	NS_IM		  => true,
	NS_MH		  => true,
	NS_PJ		  => true,
	NS_SE		  => true,
	NS_TNB		  => true,
	NS_SSMUP	  => true,
	NS_SEXCASES	  => true,
	NS_BLOG 	  => true,
	NS_BLOG_CASES => true,
	NS_USER 	  => true
);


# Contains a list of MediaWiki Namespaces categorized as being a part of OCDLA's Books Online subscription service.
$wgWhitelistedNamespaces = array(
	NS_BLOG,
	NS_BLOG_TALK,
	NS_USER,
	NS_HELP,
	NS_PUBLIC,
	NS_PUBLIC_TALK
);

// See, https://www.mediawiki.org/wiki/Manual:$wgContentNamespaces for more information
$wgContentNamespaces = array( NS_MAIN, NS_BLOG, NS_BLOG_CASES, NS_DTN_TALK);

# Pages listed in this array are public to all users.
$wgWhitelistRead =  array(
	"Welcome to The Library",
	"About the Library",
	"Main Page",
	"Special:UserLogin",
	"Special:Contact Form",
	"Special:RegistrationForm",
	"Special:Register",
	"Special:UserLogout",
	"Special:PasswordReset",
	"MediaWiki:Common.css",
	"Special:Search",
	"Resources",
	"Get Involved",
	"How To Edit",
	"OcdlaWebApp",
);

#######################	END PAGE AND NAMESPACE ACCESS CONFIGURATIONS ###################################################
########################################################################################################################






#######################	USER GROUP PERMISSIONS CONFIGURATIONS ##########################################################
########################################################################################################################

// http://www.mediawiki.org/wiki/Manual:$wgAddGroups
$wgAvailableRights[] = 'read-legcomm';
$wgAvailableRights[] = 'edit-legcomm';
$wgAvailableRights[] = 'read-subscriptions';
$wgAvailableRights[] = 'edit-subscriptions';

$wgGroupPermissions['*']['read'] = false;
$wgGroupPermissions['*']['edit'] = false; // MediaWiki 1.5+ Settings
$wgGroupPermissions['user']['edit'] = true;
$wgGroupPermissions['pageeditors']['edit'] = true;
$wgGroupPermissions['legcomm']['read-legcomm'] = true;
$wgGroupPermissions['subscriber']['read-subscriptions']	= true;
$wgGroupPermissions['subscriber']['edit-subscriptions']	= false;
$wgGroupPermissions['ignoreanalytics'] = array();
$wgGroupPermissions['sysop']['replacetext'] = true;
$wgGroupPermissions['sysop']['read-subscriptions'] = false;
$wgGroupPermissions['sysop']['edit-subscriptions'] = true;
$wgGroupPermissions['sysop']['edit-public'] = true;

// Disable registration and sign-in from the wiki front page
$wgGroupPermissions['*']['createaccount'] = false; // MediaWiki 1.5+ Settings
# $wgGroupPermissions['sysop']['editblog'] = true;      //permission "editfoo" granted to users in the "sysop" group

####################### END USER GROUP PERMISSIONS CONFIGURATIONS ######################################################
########################################################################################################################



// $wgShowExceptionDetails = true;
$wgLocaltimezone = "America/Los_Angeles";

######################################################################
$wgDefaultUserOptions['timecorrection'] = '17:00';
$wgDefaultUserOptions['date'] = 'mdy';

// Add files to the acceptable file list
$wgFileExtensions = array_merge($wgFileExtensions, array('doc', 'docx', 'xls', 'mpp', 'pdf','ppt','xlsx','jpg','tiff','odt','odg','ods','odp'));
$wgDisableUploads = false; # Enable uploads
$wgEnableUploads = true; # Enable uploads
$wgShowExceptionDetails = true;
$wgMaxPPExpandDepth = 40;
$wgPasswordAttemptThrottle 	= false;

######################################################################
# Default user options
$wgDefaultUserOptions['usebetatoolbar'] = 1;
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;
$wgDefaultUserOptions['wikieditor-preview'] = 1;








// See also: https://www.mediawiki.org/wiki/Manual:$wgTidyInternal
$wgUseTidy = false;

// Shell execution time.  Allow more memory for processes that need it.
$wgMaxShellTime = 180;

$wgUseCategoryBrowser = true;

$wgUseSiteCss = true;






######### ACTIVATE EXTENSIONS	##############
######################################################################
# Enabled Extensions. Most extensions are enabled by including the base extension file here
# but check specific extension documentation for more details
# The following extensions were automatically enabled:

// WhitelistedNamespaces/WhitelistedNamespaces : http://www.mediawiki.org/wiki/Extension:WhitelistedNamespaces
// SphinxSearch/SphinxSearch : http://www.mediawiki.org/wiki/Extension:SphinxSearch
// WikilogOcdla/WikilogOcdla : Utility extension to override some Wikilog classes in order to better implement certain Wikilog functionality.
// EditSectionClearerLink/EditSectionClearerLink : Provides custom additions to the edit specific sections of a Wiki page. Currently also provides JS for collapse/expand for sections, especially useful for touch devices.
// Wikilog/Wikilog : Use the Wikilog implementation of $wgExtraNamespaces rather than implementing this alone via the $wgExtraNamespaces.


$extensions = array(
	"AccessWhitelistedNamespaces" => array(
		"path"   => "AccessWhitelistedNamespaces/AccessWhitelistedNamespaces",
		"active" => true
	),
	"AccessBooksOnline" => array(
		"path"   => "AccessBooksOnline/AccessBooksOnline",
		"active" => true
	),
	"BooksOnlineOcdla" => array(
		"path"   => "BooksOnlineOcdla/BooksOnlineOcdla",
		"active" => false,
		"init"   => array(
			function(){BooksOnlineOcdla::SetupBooksOnlineOcdla();}
			//function(){AuthOcdlaSetup::OverrideMWLocalClass('LoginForm','SpecialUserlogin');}
		)
	),
	"BooksOnline" => array(
		"path"   => "BooksOnline/BooksOnlineSetup",
		"active" => true
	),
	"BooksOnlineOcdla" => array(
		"path"   => "BooksOnlineOcdla/BooksOnlineOcdla",
		"active" => true
	),
	"CaseReviews" => array(
		"path"   => "CaseReviews/CaseReviews",
		"active" => true
	),
	"Cite" => array(
		"path"   => "Cite/Cite",
		"active" => true
	),
	"clickpdx" => array(
		"path"   => "clickpdx/autoloader/autoloader.php",
		"active" => false,
		"init"   => array(
			function(){autoloader_boot();}  	// Autoloader is required for the custom contact form.
		)
	),
	"ContactForm" => array(
		"path"   => "ContactForm/ContactForm",
		"active" => false
	),
	"ClickpdxJavascriptFramework" => array(
		"path"   => "ClickpdxJavascriptFramework/ClickpdxJavascriptFramework",
		"active" => true,
		"init"   => array(
			function(){ClickpdxJavascriptFramework::SetupClickpdxJavascriptFramework();}
		)
	),
	"ConfirmEdit" => array(
		"path"   => "ConfirmEdit/ConfirmEdit",
		"active" => false
	),
	"EditSectionClearerLink" => array(
		"path"   => "EditSectionClearerLink/EditSectionClearerLink",
		"active" => true
	),
	"EmbedVideo" => array(
		"path"   => "EmbedVideo/EmbedVideo",
		"active" => true
	),
	"Gadgets" => array(
		"path"   => "Gadgets/Gadgets",
		"active" => false
	),
	"googleAnalytics" => array(
		"path"   => "googleAnalytics/googleAnalytics",
		"active" => false//!$ocdlaDisableExtensionGoogleAnalytics ? true : false
	),
	"InputBox" => array(
		"path"   => "InputBox/InputBox",
		"active" => true
	),
	"LinkManager" => array(
		"path"   => "LinkManager/LinkManager",
		"active" => false
	),
	"Nuke" => array(
		"path"   => "Nuke",
		"active" => false
	),
	"OAuth" => array(
		"path"   => "OAuth/OAuth",
		"active" => true
	),
	"OcdlaIndexer" => array(
		"path"   => "OcdlaIndexer/OcdlaIndexer",
		"active" => false
	),
	"ParserFunctions" => array(
		"path"   => "ParserFunctions/ParserFunctions",
		"active" => false
	),
	"PersonalLinks" => array(
		"path"   => "PersonalLinks/PersonalLinks",
		"active" => true,
		"init"	 => array(
			function(){PersonalLinksHooks::setup();}
		)
	),
	"php_mail" => array(
		"path"   => "php_mail/php_mail",
		"active" => false
	),
	"PwaOcdla" => array(
		"path"   => "PwaOcdla/PwaOcdla",
		"active" => false,
		"init" 	 => array(
			function(){PwaOcdla::SetupPwaOcdla();}
		)
	),
	"RegistrationForm" => array(
		"path"   => "RegistrationForm/RegistrationForm",
		"active" => true
	),
	"Renameuser" => array(
		"path"   => "Renameuser/Renameuser",
		"active" => false
	),
	"ReplaceText" => array(
		"path"   => "ReplaceText/ReplaceText",
		"active" => true
	),
	"SearchOcdla" => array(
		"path"   => "SearchOcdla/SearchOcdla",
		"active" => true,
		"init" => array(
			function(){SearchOcdlaHooks::SetupSearchOcdla();}
		)
	),
	"SkinPerNamespace" => array(
		"path"   => "SkinPerNamespace/SkinPerNamespace",
		"active" => false
	),
	"SphinxSearch" => array(
		"path"   => "SphinxSearch/SphinxSearch",
		"active" => true
	),
	"Teaser" => array(
		"path"   => "Teaser/Teaser",
		"active" => false
	),
	"UIFixedNav" => array(
		"path"   => "UIFixedNav/UIFixedNav",
		"active" => true,
		"init" 	 => array(
			function(){UIFixedNav::SetupUIFixedNav();}
		)
	),
	"UIDrawer" => array(
		"path"   => "UIDrawer/UIDrawer",
		"active" => true,
		"init" 	 => array(
			function(){UIDrawer::SetupUIDrawer();}
		)
	),
	"VarnishCache" => array(
		"path"   => "VarnishCache/VarnishCache",
		"active" => false, //!$ocdlaDisableExtensionVarnishCache ? true : false,
		"init"   => array(
			function(){SetupVarnishCache();}
		)
	),
	"Vector" => array(
		"path"   => "Vector/Vector",
		"active" => false
	),
	"WikiEditor" => array(
		"path"   => "WikiEditor/WikiEditor",
		"active" => true
	),
	"Wikilog" => array(
		"path"   => "Wikilog/Wikilog",
		"active" => true,
		"init"	 => array(
			function(){Wikilog::setupNamespace( NS_BLOG, 'Blog', 'Blog_talk');},
			function(){Wikilog::setupNamespace( NS_BLOG_CASES, 'Case Reviews', 'Case Reviews_talk');}
		)
	),
	"WikilogOcdla" => array(
		"path"   => "WikilogOcdla/WikilogOcdla",
		"active" => true
	),
	"notitle" => array(
		"path"   => "notitle",
		"active" => true
	)
);


// pwaocdla

//wfLoadExtensionOnly("OAuth");

foreach($extensions as $ext){

	if($ext["active"] == true){

		$file = $ext["path"];

		require("$IP/extensions/$file.php");
	
		if(!empty($ext["init"])){
	
			$functions = $ext["init"];
	
			array_walk($functions, function($func){
	
				$func();
			});
		}		
	}
}




function wfLoadExtensionOnly($name) {

	global $extensions;

	foreach($extensions as $key => $ext){
		
		$extensions[$key]["active"] = $key == $name ? true : false;
	}
}



require_once('LocalSettingsOverrides.php');
