<?php
namespace Tooma\Api;

class Pagination{
	
	private $sender  =null;
	private $details =null;
	private $nextUrl =null;

    public function __construct($sender,$details)
	{
		$this->sender = $sender;
		$this->details = $details;
	}

	public function getNext()
	{
		
		
	}

}

