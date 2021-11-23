<?php
/**
 * Hooks for EditSectionClearerLink extension
 *
 * @file
 * @ingroup Extensions
 */
$xcounter = 0;
class EditSectionClearerLinkHooks {
	static $counter = 0;
	/* Static Functions */

	/**
	 * interceptLink hook
	*/
	public static function reviseLink($this, $nt, $section, $tooltip, &$result)  {
		$linkName = "editSection-$section"; // the span around the link
		$anchorName = "editSectionAnchor-$section"; // the actual link anchor, generated in Linker::makeHeadline
		$chromeName = "editSectionChrome-$section"; // the chrome that surrounds the anchor	
		// $editUrl = preg_match('/href\=(.*)<\/h(\d+)>/mis', $result, $matches);

		// tail( $result );
		// this is what the edit link looks like before
		/*
		<span class="editsection">[
			<a href="/index.php?title=Crimes&amp;action=edit&amp;section=2" title="Edit section: Sex Crimes">
				edit
			</a>
		]</span>
		*/
		$editLink = &$result;
		$editLink = preg_replace('/(\D+)( title=)(\D+)/',
			// insert additional class, id, title and javascript attributes into the edit link
			'${1} class=\'editSectionLinkInactive\' id=\'' . $anchorName . '\''
			.' onclick="MediaWikiModule__EditSectionClearerLink_ExecuteEditLink(event,this);"' 
			//.' onmouseover="editSectionHighlightOn(' . $section . ')"'
			//.' onmouseout="editSectionHighlightOff(' . $section . ')"'
			.' title=$3',
			// create the new result
			$editLink);
		
		$result = preg_replace('/<\/span>/', '<span class="editSectionChromeInactive" id=\'' .
			$chromeName . '\'>&#8690;</span></span>', $result);

		// while resourceloader loads this extension's css pretty late, it's still
		// overriden by skins/common/shared.css.  to get around that, insert an explicit style here.
		// i'd welcome a better way to do this.
		$result = preg_replace('/(<span class=\"editsection)/', '${1} editSectionInactive" style="float:none" id="' . $linkName, $result);

		return true;
	}
/** check this!!! **/


	public static function onParserBeforeInternalParse( &$parser, &$text, &$strip_state ) {
		// tail( $parser );
		return true;
	}
	/**
	 * interceptSection hook
	 */
	public static function reviseSection($this, $section, &$result, $editable)  {
		// skip section 0, since it has no edit link.
		global $xcounter;
		if(++$xcounter==1) {
//				$this->mSections[4] = array();
				//unset($this->mSections[5]);
				//unset($this->mSections[6]);
				//unset($this->mSections[count]);				
			}

		if( $section === 0 ) {
			return true;
		}
		
		// swap the section edit links to the other side.  for some reason
		// Linker::makeHeadline places them on the left and then they're moved
		// to the right with a css hack.  That's lame, but I get the
		// feeling that changing it might make some folks unhappy.  For example,
		// anyone else who's written a nasty kludge like this
		

		$result = preg_replace( '/(<mw\:editsection.*<\/mw:editsection>)\s*(<span class="mw-headline".*<\/span>)/', '$2 $1', $result );
		
		// @jbernal
		preg_match('/<h\d+>(.*)<\/h(\d+)>/mis', $result, $matches);
		//		if($matches[2]>2) return true;
		if(isset($matches[2])&&isset($matches[1]))
		{
		$heading = '<h'.$matches[2].'  class="mw-customtoggle-'.$section.'">'.$matches[1].'</h'.$matches[2].'>';
		}


		

		// A DIV can span sections in wikimarkup, which will break this code.  Such
		// section-spanning DIVs are rare.  If one appears, leave it alone.
		//
		// count opening DIVs.
		preg_match_all( '/(<\s*div[\s>])/i', $result, $matches );
		$openingDivs = count( $matches[0] );

		// count closing DIVs.
		preg_match_all( '/(<\s*\/div[\s>])/i', $result, $matches );
		$closingDivs = count( $matches[0] );

		if ( $openingDivs !== $closingDivs ) {
			return true;
		}
				
		// wrap everything in a div.
		if ( $editable ) {
			// $result = '<div class="editableSection mw-collapsible" id="articleSection-' .
			$result = '<div class="editableSection mw-collapsible" id="mw-customcollapsible-'.
			 $section . '"' .
				// ' onmouseover="editSectionActivateLink(\'' . $section . '\')" onmouseout="editSectionInactivateLink(\'' . $section . '\')"'.
				'>' . 
				$result . 
				'</div>';
		} else {
			$result = "<div class='nonEditableSection mw-collapsible' id='mw-customcollapsible-" . $section . "'>" . $result . '</div>';
		}
		if(isset($heading))
		{
			$result = $heading . preg_replace('/<h\d+>.*<\/h\d+>/mis','',$result);
		}
		// tail( $result );
		return true;
	}

	/**
	 * Load resources with ResourceLoader (in this case, CSS and js)
	 */
	public static function addPageResources( &$outputPage, $parserOutput ) {
		// $outputPage->addModules( 'ext.editSectionClearerLink' );
		// tail($parserOutput);  
		return true;
	}
	/*public static function onInternalParseBeforeLinks( Parser &$parser, &$text ) {		//tail($parser);
		return true;
	}
	*/
}
