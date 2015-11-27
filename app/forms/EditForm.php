<?php

namespace Forms;


use Tulinkry\Forms;
use Model;

class EditForm extends Forms\Form
{

	protected $galleryStorage;
	protected $photos;

	public function __construct ( $id, $photos, $galleries, $storage, $parent )
	{
		parent::__construct ();

		$this -> photos= $photos;
		//$this -> galleryStorage = $model;

		$this -> addHidden ( "thumbnail" );
		$this -> addText ( "id", "ID" );
		$this -> addText ( "content", "Obsah" );

        $galleries = $galleries -> all ();
        $items = [];
        foreach ( $galleries as $g )
            $items [ $g -> id ] = $g -> name;

		$this -> addSelect ( "gallery", "Galerie", $items );


		$remove = function ( $button ) use ( $storage, $parent )
		{ 
			// remove callback
			//print_r ( $button -> parent -> name );
	        $storage -> delete ( $button -> form -> values [ "id" ] );
	        //print_r ( get_class($button -> parent -> parent) );
			//$button -> parent -> parent -> remove ( $button -> parent );
	        $parent -> invalidateControl ();
		};

		$rotate = function ( $button ) use ( $storage, $parent )
		{
	        $parent -> invalidateControl ();
	        return $storage -> rotate ( $button -> form -> values [ "id" ], 90 );
		};

		$this -> addSubmit ( "remove", "Odebrat" )
				   -> setAttribute ( "class", "ajax" )
				   -> setValidationScope ( FALSE )
				   -> onClick [] = $remove;

		$this -> addSubmit ( "rotate", "Rotovat o 90" )
				   -> setAttribute ( "class", "ajax" )
				   -> setValidationScope ( FALSE )
				   -> onClick [] = $rotate;

		$e = $photos -> item ( $id );
		$this -> setDefaults ( $e -> toArray () );

	   	$this -> addSubmit ( "submit", "Uložit" )
	   		  -> setAttribute ( "class", "ajax" );

/*

		$storage = $this -> galleryStorage;
		$remove = function ( $button ) use ( $storage, $parent )
		{ 
			// remove callback
			//print_r ( $button -> parent -> name );
	        $storage -> delete ( $button -> parent -> name );
	        //print_r ( get_class($button -> parent -> parent) );
			$button -> parent -> parent -> remove ( $button -> parent );
	        $parent -> invalidateControl ();
		};

		$rotate = function ( $button ) use ( $storage, $parent )
		{
	        $parent -> invalidateControl ();
	        return $storage -> rotate ( $button -> parent -> name, 90 );
		};

		$files = $this -> addDynamic ( "files", function ( $container ) use ( $remove, $rotate )
		{
			$container -> addHidden ( "thumbnail" );
			$container -> addText ( "id", "ID" );
			$container -> addText ( "content", "Popis" );
			$container -> addSelect ( "gallery", "Galerie" );
			$container -> addSubmit ( "remove", "Odebrat" )
					   -> setAttribute ( "class", "ajax" )
					   -> setValidationScope ( FALSE )
					   -> onClick [] = $remove;

			$container -> addSubmit ( "rotate", "Rotovat o 90" )
					   -> setAttribute ( "class", "ajax" )
					   -> setValidationScope ( FALSE )
					   -> onClick [] = $rotate;
		}, 0 );

	   	$this -> addSubmit ( "submit", "Uložit" )
	   		  -> setAttribute ( "class", "ajax" );
*/


	}

	public function process ( $form )
	{
		$values = $form -> values;
		$item = $this -> photos -> item ( $values [ "id" ] );

		$item -> content = $values [ "content" ];
		//$item -> gallery = $values [ "gallery" ];

		$this -> photos -> update ($item);

	}

}
