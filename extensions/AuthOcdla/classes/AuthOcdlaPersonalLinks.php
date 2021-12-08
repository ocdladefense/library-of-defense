<?php

class AuthOcdlaPersonalLinks
{
	/**
	 * var manager
	 *
	 * Link Manager.  This is the link manager that
	 * contains settings passed to this object.
	 */	
	protected $manager;
	
	/**
	 * var data
	 *
	 * Data is an array of attributes containing information
	 * on the links that are to be rendered.
	 */
	protected $data = array();
	
	/**
	 * var attributes
	 *
	 * A list of attributes that are associated with this.
	 */
	protected $attributes = array();
	
	
	public function __construct(AuthOcdlaPersonalLinksManager $manager)
	{
		$this->manager = $manager;		
	}
	
	public function getAttribute($attrName)
	{
		return $this->attributes[$attrName];
	}
	
	public function addLink($linkName,$href,$options=array())
	{
	
	}
	
	public function addAttribute($attrName,$attrValue)
	{
		$this->attributes[$attrName]=$attrValue;
	}
	
	public function removeLink($linkName)
	{
		
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	private function setLinkDefaults(&$link)
	{
		if(!isset($link['accesskey']))
			$link['accesskey'] = '';
		if(!isset($link['title']))
			$link['title'] = $link['name'];
		if(!isset($link['id']))
			$link['id'] = '';
		else $link['id'] = " id='{$link['id']}'";
	}
	
	public function formatHtml($link)
	{
		$this->setLinkDefaults($link);
		return "<li{$link['id']}><a accesskey='{$link['accesskey']}' title='{$link['title']} [{$link['accesskey']}]' href='{$link['href']}'>{$link['name']}</a></li>";
	}

	public function getAsHtml()
	{
		$links = array_map(function($link){
			return $this->formatHtml($link);},$this->data);
		return "<ul>" .implode($links) ."</ul>";
	}
	
	public function getLinks()
	{

		global $wgAuthOcdla_LogoutURL;

		$keys = func_get_args();
		// $keys = is_array($key)?$keys:array($keys);
		$links = array(
			'pt-login' 	=> array(
				'title' 		=> 'You are encouraged to log in; however, it is not mandatory [ctrl-option-o]',
				'href' 			=> $this->manager->getLoginurl(),
				'name' 			=> 'Log in'
			),
			'pt-userpage' => array(
				'accesskey' => '.',
				'title' => 'Your user page',
				'href' => "/User:".$this->manager->mwUser->mName,
				'name' => $this->manager->mwUser->mName
			),
			'pt-mytalk' => array(
				'accesskey' => 'n',
				'title' => 'Your talk page',
				'class' => 'new',
				'href' => '/User_talk:'.$this->manager->mwUser->mName,
				'name' => 'My talk',
			),
			'pt-preferences' => array(
				'title' => 'Your preferences',
				'href' => '/Special:Preferences',
				'name' => 'My preferences'
			),
				'pt-wikilog-blog' => array(
					'title' => 'Add a Blog Post',
					'href' => '/index.php?title=Blog:Main&action=wikilog',
					'name' => 'Add Blog Post'
				),
				'pt-wikilog-case-review' => array(
					'title' => 'Add a Case Review',
					'href' => '/index.php?title=Blog:Case_Reviews&action=wikilog',
					'name' => 'Add Case Review'
				),
				'pt-emailer-review' => array(
					'title' => 'Review new posts.',
					'href' => '/ocdlaemail/publish.php',
					'name' => 'Emailer Review'
				),
				'pt-emailer-action' => array(
					'title' => 'Notify subscribers of new posts.',
					'href' => '/ocdlaemail/sendmail.php',
					'name' => 'Send Email'
				),
				'pt-legcomm' => array(
					'title' => 'Legislative Affiars Committee',
					'href' => '/Legcomm:Home',
					'name' => 'Legislative Affairs'
				),
				'pt-manage-groups' => array(
					'title' => 'Manage Groups',
					'href' => '/Special:UserRights/'.$this->manager->mwUser->mName,
					'name' => 'Manage Groups'
				),
				'pt-watchlist' => array(
					'accesskey' => 'l',
					'title' => 'A list of pages you are monitoring for changes',
					'href' => '/Special:Watchlist',
					'name' => 'My watchlist'
				),
				'pt-mycontris' => array(
					'accesskey' => 'y',
					'title' => 'A list of your contributions',
					'href' => '/Special:Contributions/'.$this->manager->mwUser->mName,
					'name' => 'My contributions'
				),
				'pt-logout' => array(
					'title' => 'Log out',
					'href' => $wgAuthOcdla_LogoutURL,
					'name' => 'Log out'
				)
		);
		
		$keys = array_flip($keys);
		return array_intersect_key($links,$keys);
	}
}