<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors','0');

// So extensions (and other code) can check whether they're running in API mode
define( 'MW_API', true );
# Bail on old versions of PHP.  Pretty much every other file in the codebase
# has structures (try/catch, foo()->bar(), etc etc) which throw parse errors in
# PHP 4. Setup.php and ObjectCache.php have structures invalid in PHP 5.0 and
# 5.1, respectively.
if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.3.2' ) < 0 ) {
	// We need to use dirname( __FILE__ ) here cause __DIR__ is PHP5.3+
	require( dirname( __FILE__ ) . '/includes/PHPVersionError.php' );
	wfPHPVersionError( 'index.php' );
}

# Initialise common code.  This gives us access to GlobalFunctions, the
# AutoLoader, and the globals $wgRequest, $wgOut, $wgUser, $wgLang and
# $wgContLang, amongst others; it does *not* load $wgTitle
if ( isset( $_SERVER['MW_COMPILED'] ) ) {
	require ( 'phase3/includes/WebStart.php' );
} else {
	require ( __DIR__ . '/includes/WebStart.php' );
}


// Set a dummy $wgTitle, because $wgTitle == null breaks various things
// In a perfect world this wouldn't be necessary
$wgTitle = Title::makeTitle( NS_MAIN, 'API' );

/* Construct an ApiMain with the arguments passed via the URL. What we get back
 * is some form of an ApiMain, possibly even one that produces an error message,
 * but we don't care here, as that is handled by the ctor.
 */
$processor = new ApiMain( RequestContext::getMain(), $wgEnableWriteAPI );



/* These items don't work either because MediaWiki resets these headers */
// header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: application/json; charset=utf-8');
// ini_set('display_errors',1);


$articleId = $_GET['id'];

$article = Article::newFromId($articleId);
$parsed = $article->getParserOutput(); // this is an instance of ParserOutput
$theText = $parsed->getText();

$page = WikiPage::newFromId($articleId);
$raw = $page->getText();
$wikiText = str_replace("\n\n\n","\n",$raw);
$wikiText = str_replace("\n\n","\n",$wikiText);


// ->render();

$sections = $parsed->getSections();
// var_dump($sections);exit;
$mwSections = array();

for($i=0; $i<count($sections);$i++){
	array_push($mwSections, generateMWSectionObject($i,$articleId, $sections[$i],$sections[$i+1],$wikiText));
}
// var_dump($sections);
// exit;
// var_dump($parser->getOutput()); -- doesn't work
function generateMWSectionObject($sectionNo,$articleId,$currentSection,$nextSection,$wikiText){
	$o = new stdClass;
	$o->docId = $articleId . '-'.$sectionNo;
	$o->parentTitle = $currentSection['fromtitle'];
	$o->title = $currentSection['line'];
	
	$currentContentStart = $currentSection['byteoffset'] + mb_strlen($currentSection['line']);

	$o->content = getSectionText($currentContentStart,$nextSection['byteoffset'],$wikiText);
	$o->anchor = $currentSection['anchor'];
	
	return $o;
}


function getSectionText($sectionStart,$sectionEnd,$wikiText){
	$start = $sectionStart;
	$end = $sectionEnd;
	$length = $end - $start;
	
	return mb_strcut($wikiText,$start,$length);
}


// $mwIndexDocuments = array_map('createMWIndexDocument',$mwSections);



function createMWIndexDocument($mwSection,$articleId,$data=array()){
	$obj = new stdClass;
	
	// We'll need some consistent way to code sections
	$obj->docId = $articleId.'-sectionId';
	$obj->content = $mwSection->text;
	$obj->title = 'some title';
	$obj->author = 'author';
	$obj->alternate_author = 'another author';
	return $obj;
}

print json_encode($mwSections);