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
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: application/json; charset=utf-8');
// ini_set('display_errors',1);


// $namespaceId = $_GET['id'];


// print_r(MWNamespace::getValidNamespaces());

//$ns = new OcdlaNamespace($namespaceId);
$bonNamespaces = [500,504,508,510,512,514,522,518,520];
$bonNamespaces = [
//	NS_DTN,
//	NS_DNB,
//	NS_FSM,
//	NS_IM,
	NS_MH,
	NS_PJ,
	NS_SE,
// 	NS_SSMUP,
	// NS_SSM, deprecated by @jbernal, 2018-09-12
	NS_TNB,
//	NS_VET,
//	NS_SEXCASES
];

print OcdlaNamespace::toJson($bonNamespaces);
	
	
print OcdlaNamespace::toJson();
// print_r($ns->getPages());

  




// https://www.mediawiki.org/wiki/Manual:Database_access
class OcdlaNamespace
{
	public $nsId;
	
	public $nsName;
	
	public $numChapters;
	
	public $nsPages = array();
	
	public $nsNamePretty;
	
	public function __construct($id)
	{
		$this->nsId = $id;
		$this->nsName = MWNamespace::getCanonicalName($id);
		$this->numChapters = 0;
		$dbr = wfGetDB(DB_MASTER);
		$res = $dbr->select(
			'page',                                   // $table
			array( 'page_id', 'page_title', 'page_order' ),            // $vars (columns of the table)
			('page_namespace = '.$id),                              // $conds
			__METHOD__,                                   // $fname = 'Database::select',
			array( 'ORDER BY' => 'page_order' )        // $options = array()
		);
		foreach($res as $page){
			$this->numChapters++;
			$page->link = $this->nsName . ':'.str_replace('?','%3F',$page->page_title);
			$page->titleNoNamespace = $this->formatPageTitleWithoutNs($page->page_title);
			$page->prettyTitle = $this->formatPageTitlePretty($page->page_title);
			$page->chapterNo = $this->formatChapterNo($page->titleNoNamespace);
			array_push($this->nsPages,$page);
		}
		
		$this->nsNamePretty = str_replace('_',' ',$this->nsName);
	}
	
	private function formatPageTitleWithoutNs($title)
	{
		return substr($title,strlen($this->nsName)+1);
	}
	
	private function formatChapterNo($title)
	{
		// Get the last position of `_`
		if(strpos($title,'Chapter')===0)
		{
			$titleLength = strlen($title);
			$offset = strlen('Chapter')+1;
			if($titleLength - $offset<3)
			{
				return substr($title,$offset);
			}
			else
			{
				$end = strpos($title,'_',$offset)-1;
				return substr($title,$offset,($end-$offset)+1);
			}
		}
		else
		{
			$pos = strrpos($title,'_');
			return substr($title,$pos+1);
		}
	}
	
	private function formatPageTitlePretty($title)
	{
		if(strpos($title,$this->nsName)===false)
		{
			return str_replace('_',' ',$title);		
		}
		else
		{
			return str_replace('_',' ',substr($title,strlen($this->nsName)+1));
		}
	}
	
	public function getPages()
	{
		return $this->nsPages;
	}	
	
	public function getPagesAsJson()
	{
		return json_encode($this->nsPages);
	}
	
	public static function toJson(array $ids)
	{
		$page_sets = array_map(function($ns){ return new OcdlaNamespace($ns); },$ids);
		return json_encode($page_sets);
	}
	
}