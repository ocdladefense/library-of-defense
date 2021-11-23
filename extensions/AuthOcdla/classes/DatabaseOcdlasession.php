<?php

class DatabaseOcdlasession extends DatabaseMysql
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
	 * 
	 */
	protected $data;
	
	/**
	 * var attributes
	 *
	 * A list of attributes that are associated with this.
	 */
	protected $attributes = array();
	
	public function q($query)
	{
		return $this->doQuery($query);
	}
	
	public function fetchObj($res)
	{
		return $this->fetchObject($res);
	}
	
	public function getSessionObject()
	{
		global $wgOcdlaSessionCookieName;
		
		$result = $this->q('SELECT * FROM my_aspnet_Sessions WHERE SessionID=\''
			.$_COOKIE[$wgOcdlaSessionCookieName] . '\'');
		$this->data = $this->fetchObj($result);
		
		
		return $this->data;
	}
	
	public function getSamlData()
	{
		return unserialize($this->getSessionObject()->Saml);
	}
	
	public function getContactId()
	{
		return $this->getSamlData()['ContactId'][0];
	}
	
	public function __toString()
	{
		return get_class($this);
	}
}