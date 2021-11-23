<?php

class AuthOcdlaPersonalLinksAuthenticated extends AuthOcdlaPersonalLinks
{
	
	public function __construct($manager)
	{
		parent::__construct($manager);
		$this->addAttribute('logged_in',true);
		$this->data = $this->getLinks(
			'pt-userpage',
			'pt-mytalk',
			'pt-preferences'
		);

		
		if(true || $this->manager->mwUser->mId == 3 || $this->manager->mwUser->mId == 25060)
		{
			$this->data += $this->getLinks('pt-wikilog-blog','pt-wikilog-case-review');
		}

		if($this->manager->mwUser->mId == 3 || $this->manager->mwUser->mId == 25060)
		{
			$this->data += $this->getLinks('pt-emailer-review','pt-emailer-action');
		}

		if($this->manager->isLegComm())
		{
			$this->data += $this->getLinks('pt-legcomm');
		}

		if($this->manager->isSysOp())
		{
			$this->data += $this->getLinks('pt-manage-groups');
		}

		// Add the final three links here:
		$this->data += $this->getLinks('pt-watchlist','pt-mycontris','pt-logout');
	}
}