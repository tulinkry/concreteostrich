<?php

namespace Model;

use Tulinkry\Model\Facebook;

class FbImportModel
{

	protected $eventModel;
	protected $postsModel;

	protected $user = "";

	public function __construct ( Facebook\EventModel $events, Facebook\PostModel $posts)
	{
		$this -> eventModel = $events;
		$this -> postsModel = $posts;

		$this -> user = "ConcreteOstrich";
	}


	public function getAllEvents ( $since )
	{
		return $this -> eventModel -> by ( $this -> user, [ "since" => $since ] );
	}

	public function getAllPosts ( $since )
	{
		return $this -> postsModel -> by ( $this -> user, [ "since" => $since ] );
	}

	public function item ( $id )
	{
		return $this -> postsModel -> item ( $this -> user, $id );
	}

}