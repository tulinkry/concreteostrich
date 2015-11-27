<?php

namespace Controls;

use Tulinkry;

class EditGrid extends Tulinkry\Grid
{

	protected $storage;

	public function __construct ( $storage )
	{
		parent::__construct ();
		$this -> storage = $storage;
        $this -> addColumn ( 'id' );
        $this -> addColumn ( 'thumbnail', 'Thumbnail' );
        //$this -> addColumn ( 'datum', 'Uploaded' ) -> enableSort();
        $this -> addColumn ( 'content', "Popisek" );
        $this -> addColumn ( "gallery", "Galerie" );
        $this -> addColumn ( "rotate", "Rotace" );
        $this -> addColumn ( "delete", "D" );
       	$this -> addCellsTemplate ( __DIR__ . "/grid.columns.latte" );		
	}

	public function handleRotate ( $id, $angle )
	{
		/*
		$rotate = function ( $button ) use ( $storage, $parent )
		{
	        $parent -> invalidateControl ();
	        return $storage -> rotate ( $button -> form -> values [ "id" ], 90 );
		};
		*/

		$this -> invalidateControl ();
		$this -> storage -> rotate ( $id, $angle );
	}

	public function handleDelete ( $id )
	{
		/*
		$remove = function ( $button ) use ( $storage, $parent )
		{ 
			// remove callback
			//print_r ( $button -> parent -> name );
	        $storage -> delete ( $button -> form -> values [ "id" ] );
	        //print_r ( get_class($button -> parent -> parent) );
			//$button -> parent -> parent -> remove ( $button -> parent );
	        $parent -> invalidateControl ();
		};
		*/

		$this -> invalidateControl ();
		$this -> storage -> delete ( $id );
	}

}