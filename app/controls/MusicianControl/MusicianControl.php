<?php

namespace Controls;

use Tulinkry\Application\UI;

class MusicianControl extends UI\Control
{
	protected $galleryStorage;
	protected $musicians;
	protected $musician;

	public function __construct ( $galleryStorage, $model, $id )
	{
		$this -> musicians = $model;
		$this -> musician = $model -> item ( $id );
		$this -> galleryStorage = $galleryStorage;
	}


	public function render ()
	{
		$this -> template -> setFile ( __DIR__ . "/musicianControl.latte" );

		$this -> template -> musician = $this -> musician;
		$this -> template -> render ();
	}

}