<?php
ob_start();
error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors','1');
// require('../LocalSettings.php');
require('../SiteSpecificSettings.php');
$dbhost = $wgDBserver;
$dbuser = $wgDBuser;
$dbpass = $wgDBpassword;
$dbname = $wgDBname;


include("db.php");
include("select.php");

define("CONFIG_SHOWSQL", 0);

$val = sel("SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = 'wikilog_email_subscriptions'")->executeSingle();
if($val != 'wikilog_email_subscriptions')
{
	sel("CREATE TABLE `wikilog_email_subscriptions` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `email` varchar(255) NOT NULL,
	  `guid` varchar(255) NOT NULL,
	  `activated` int(10),
	  KEY `id_index` (`id`)
	);")->execute();
}

$val = sel("SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = 'wikilog_email_records'")->executeSingle();
if($val != 'wikilog_email_records')
{
	sel("CREATE TABLE `wikilog_email_records` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `title` varchar(255) NOT NULL,
	  `guid` varchar(255) NOT NULL,
	  KEY `id_index` (`id`)
	);")->execute();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	 <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<?php
if(function_exists("head_content")){
	head_content();
}
?>
</head>
<body>
<?php
if(function_exists("body_content")){
	body_content();
}
?>
</body>
</html><?