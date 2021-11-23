<?php


error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('memory_limit','256M');
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/www.hsolc.org-php_errors.log");

require( drupal_root().'/sites/default/modules/clickpdx/autoloader/autoloader.module');
autoloader_boot();

function drupal_root(){
	$was_dir = getcwd();
	chdir('../');
	$dir = explode('/',__DIR__);
	array_pop($dir);
	$drupal = implode('/',$dir);
	chdir($was_dir);
	return $drupal;
}

