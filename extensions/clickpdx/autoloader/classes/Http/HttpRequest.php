<?php
namespace Http;


class HttpRequest
{


	public function __construct()
	{
		$this->globals = $_SERVER;
		$this->action = 'sendMail';
	}




}