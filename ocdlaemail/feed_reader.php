<?php
$cachedir = dirname(__FILE__)."/cache";
$cachetime = "86400";

require_once(dirname(__FILE__).'/SimplePie/autoloader.php');

// Add more URL(s) here.
$urls[] = 'http://libraryofdefense.ocdla.org/index.php/Blog:Main?feed=rss';
$urls[] = 'http://libraryofdefense.ocdla.org/index.php/Blog:Case_Review?feed=rss';
$urls[] = 'http://libraryofdefense.ocdla.org/index.php/Blog:Case_Reviews?feed=rss';

// Call SimplePie
$feed = new SimplePie();

$feed->set_feed_url($urls);

$feed->enable_cache(false);

// Init feed
$feed->init();
// Make sure the page is being served with the UTF-8 headers.
$feed->handle_content_type();
$items = $feed->get_items();