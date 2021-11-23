<?php
include('feed_reader.php');
$guid = $_REQUEST["guid"];
$item = null;
foreach($items as $i){
	$link = $i->get_link();
	if($guid == md5($i->get_id())){
		$item = $i;
		break;
	}
}
if(!$item){
	die('unknown blog post');
}
$title = $item->get_title();
$guid = md5($item->get_id());
$desc = $item->get_description();
$link = $item->get_link();
