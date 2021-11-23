<?php
# Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/RegistrationForm/RegistrationForm.php" );
EOT;
	exit( 1 );
}
 
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'RegistrationForm',
	'author' => 'José Bernal',
	'url' => 'https://www.mediawiki.org/wiki/Extension:RegistrationForm',
	'descriptionmsg' => 'registrationform-desc',
	'version' => '0.0.0.1',
);
 
$dir = dirname( __FILE__ ) . '/';
# Use alternate / additional settings when upgrading MediaWiki to a more recent version. 
# require_once __DIR__ . "/ContactFormMWUpgrade.php";

$wgExtensionMessagesFiles['RegistrationForm'] = $dir . 'RegistrationForm.i18n.php';

# Location of an aliases file (Tell MediaWiki to load it)
$wgExtensionMessagesFiles['RegistrationFormAlias'] = __DIR__ . '/RegistrationForm.alias.php';

# Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgAutoloadClasses['SpecialRegistrationForm'] = __DIR__ . '/SpecialRegistrationForm.php';

# Tell MediaWiki about the new special page and its class name
$wgSpecialPages['RegistrationForm'] = 'SpecialRegistrationForm';