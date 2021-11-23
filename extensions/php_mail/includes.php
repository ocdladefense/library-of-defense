<?php


function getHtmlBody(){
	$html = file_get_contents('template.html', true);
	// $boundary = '----=_NextPart_';
	$htmlBody = str_replace("[Announcements]", getAnnouncements(), $html);
	return $htmlBody;
}


function getTextBody(){
	$text_body = file_get_contents('template.txt', true);
	$text_body = str_replace("[Announcements]",getAnnouncements( "text" ),$text_body);
	return $text_body;
}


function getAnnouncements( $style = "html" ) {
	$connection = mysql_connect('localhost','jbernal','8a9F313a57');
	mysql_query('SET NAMES utf8');
	//if(!$connection) $error_msg="Unable to connect to database.";
	$use = mysql_query('use drupal');	

	$query = "SELECT n.nid, n.title, a.isodate AS _date from node n JOIN node_revisions v ON (n.vid=v.vid) JOIN clickpdx_iso_date a ON(n.vid=a.vid) WHERE n.status=1 AND n.type='announcement' ORDER BY _date DESC LIMIT 10";

	$exec = mysql_query( $query );
	
	$list = "";
	
	if( $style == "html" ) {
		while( $row = mysql_fetch_assoc( $exec ) ) {
			$list .= "<li>" . date('F j',strtotime($row['_date'])) . " - <a target='_new' href='http://www.hsolc.org/node/{$row['nid']}'>" . $row["title"] . "</a></li>";
		}
		
		return "<ul>" . $list . "</ul>";
	} else if( $style == "text" ) {
		while( $row = mysql_fetch_assoc( $exec ) ) {
			$list .= date('F j',strtotime($row['_date'])) ." - {$row['title']}:\nhttp://www.hsolc.org/node/{$row['nid']}\n\n";
		}		
		return $list;
	}
}