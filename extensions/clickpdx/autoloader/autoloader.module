<?php


/**
 * Implementation of hook_boot().
 */
function autoloader_boot() {
	spl_autoload_register("autoloader_autoload",true,false);	
}


function autoloader_autoload($class){
	$base_dir=realpath(__DIR__);
	$class = str_replace('\\','/',$class);
	$location = $base_dir .'/classes/'.$class .'.php';
	if(file_exists($location)){
		include($location);
	}
	//else print "Could not find $location.";
}


/**
 * Get the document root for the current Drupal installation.
 * $_SERVER['DOCUMENT_ROOT'] is not reliable across all
 * systems, so we need a way to get the correct value.
 *
 * @return (string)
 */
function autoloader_document_root() {
  $absolute_dir = dirname(__FILE__);
  $relative_dir = \drupal_get_path('module', 'autoloader');
  return substr($absolute_dir, 0, -1 * (1 + strlen($relative_dir)));
}