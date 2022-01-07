<?php

class PersonalLinksAnonymous extends PersonalLinks
{
	
	public function __construct(PersonalLinksManager $manager) {

		parent::__construct($manager);

		$this->data = $this->getLinks('pt-login');
		
		$this->addAttribute('logged_in',false);
	}

}