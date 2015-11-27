<?php

namespace AdminModule\Controls;

use Tulinkry\Application\UI;
use Tulinkry;

class GalleryForm extends UI\Form
{

	public function __construct ( $parent = NULL, $name = NULL )
	{
		parent::__construct ( $parent, $name );

		$this -> addText ( "name", "Jméno" );
		$this -> addTextArea ( "content", "Popis" );


		$this -> addDate ( "datum", "Datum" )
			  -> setMask ( "d. m. Y H:i" )
			  -> setDefaultValue ( new Tulinkry\DateTime );


		$this -> addSelect ( "hidden", "Viditelná", [ 1 => "Ne", 0 => "Ano" ] )
			  -> setDefaultValue ( 0 ); 


		$this -> addHidden ( "id" );
	}

};