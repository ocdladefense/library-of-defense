<?php

class WikilogPagerText
{
	private $pagerText;
	
	private $moreText;
	
	
	public function __construct($item,$caller,$params)
	{
		if(strpos($item->mTitle->mPrefixedText,'Blog:Case')===0)
		{
			// $this->moreText = $caller->parse( wfMsgForContentNoTrans( 'wikilog-summary-more', $params ) );
			$this->moreText = wfMessage('wikilog-case-review-summary-more',$params);
		}
		else {
			$this->moreText = $caller->parse( wfMsgForContentNoTrans( 'wikilog-summary-more', $params ) );
		}
	}
	public function __toString()
	{
		return $this->moreText."";
	}
}