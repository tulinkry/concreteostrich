<?php

namespace AdminModule\Controls;

use Tulinkry\Application\UI;
use Controls;

class GalleryControl extends UI\Control
{
	protected $galleryStorage;
	protected $model;
	protected $gallery;

	public function __construct ( $galleryStorage, $model, $id )
	{
		$this -> model = $model;
		$this -> gallery = $model -> item ( $id );
		$this -> galleryStorage = $galleryStorage;
	}

	public function handleDelete ()
	{
		$m = $this -> gallery;
		if ( $m )
		{

			try {
				foreach ( $m -> photos as $photo )
					$this -> galleryStorage -> delete ( $photo -> id );
			} catch ( \Exception $e )
			{
				$this -> presenter -> flashMessage ( "Některé fotky z galerie jsou spojené s událostmi nebo příspěvky, proto bylo mazání zrušeno.
													  Pokud chcete smazat galerii, musíte nejdříve smazat události a příspěvky, které používají fotky z této
													  galerie", "error" );
				$this -> presenter -> invalidateControl ( "flashMessageArea" );
				$this -> invalidateControl ();
				return;				
			}

			try {
				$this -> model -> remove ( $m );
				
			} catch ( \Exception $e )
			{
				$this -> presenter -> flashMessage ( "Vyskytla se chyba u mazání, galerie nebyla smazána" );
				$this -> presenter -> invalidateControl ( "flashMessageArea" );
				$this -> invalidateControl ();
				return;
			}
			$this -> gallery = null;
			$this -> presenter -> flashMessage ( "Galerie smazána" );
		}
		$this -> presenter -> invalidateControl ( "flashMessageArea" );
		$this -> invalidateControl ();
	}

	public function handleEdit ()
	{
		$this -> template -> edit = 1;
		$this -> invalidateControl ();
	}

	public function handleHide ()
	{
		$this -> gallery -> hidden = 1;
		$this -> model -> update ( $this -> gallery );
		$this -> presenter -> invalidateControl ( "galleries" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );		
		$this -> invalidateControl ();
	}

	public function handleUnhide ()
	{
		$this -> gallery -> hidden = 0;
		$this -> model -> update ( $this -> gallery );
		$this -> presenter -> invalidateControl ( "galleries" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );		
		$this -> invalidateControl ();
	}


	protected function createComponentEditGalleryForm ( $name )
	{
		$form = new GalleryForm;

		$form -> addSubmit ( "submit", "Uložit" )
			  -> setAttribute ( "class", "ajax" )
			  -> onClick [] = callback ( $this, "edit" );

		$form -> setDefaults ( $this -> gallery -> toArray () );

		return $form;

	}




	public function edit ( $button )
	{
		$values = $button -> form -> getValues();
		$m = $this -> model -> item ( $values [ "id" ] );
		if ( $m )
		{
			unset ( $values [ "id" ] );

			try{
				$this -> model -> update_array ( $m, $values );
				
			} catch ( \Exception $e )
			{
				$button -> form -> addError ( "Nepodařilo se uložit data" );
				$this -> template -> edit = 1;
				$this -> invalidateControl ();
				return;			
			}

			$this -> presenter -> flashMessage ( "Změny uloženy." );
			if ( ! $this -> isAjax () )
				$this -> redirect ( "this" );
			$this -> presenter -> invalidateControl ( "flashMessageArea");
			$this -> invalidateControl ();
		}
		else
		{
			$this -> presenter -> flashMessage ( "Nastala chyba." );
			if ( ! $this -> isAjax () )
				$this -> redirect ( "this" );
			$this -> presenter -> invalidateControl ( "flashMessageArea");
			$this -> invalidateControl ();
		}
	}




	public function render ()
	{
		$this -> template -> setFile ( __DIR__ . "/galleryControl.latte" );

		$this -> template -> gallery = $this -> gallery;
		$this -> template -> render ();
	}

}