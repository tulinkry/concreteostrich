<?php

namespace AdminModule\Controls;

use Tulinkry\Application\UI;
use Controls;

class MusicianControl extends Controls\MusicianControl
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

	public function handleDelete ()
	{
		$m = $this -> musician;
		if ( $m )
		{
			$photo = $m -> photo;
			$m -> setPhoto ( NULL );
			$this -> galleryStorage -> delete ( $photo -> id );
			$this -> musicians -> remove ( $m );
		}
		$this -> invalidateControl ();
		$this -> presenter -> invalidateControl ( "flashMessageArea" );
		$this -> flashMessage ( "Hudebník smazán" );
	}

	public function handleEdit ()
	{
		$this -> template -> edit_musician = 1;
		$this -> invalidateControl ();
	}


	protected function createComponentEditMusicianForm ( $name )
	{
		$form = new UI\Form;

		$form -> addText ( "name", "Jméno" );
		$form -> addText ( "surname", "Příjmení" );
		$form -> addTextArea ( "content", "Popis" );


		$form -> addUpload ( "photo", "Fotka" );

		$form -> addSubmit ( "submit", "Vložit" )
			  -> setAttribute ( "class", "ajax" )
			  -> onClick [] = callback ( $this, "edit" );

		$form -> addHidden ( "id" );
		$form -> setDefaults ( $this -> musician -> toArray () );

		return $form;

	}


	public function edit ( $button )
	{
		$values = $button -> form -> getValues(TRUE);
		$m = $this -> musicians -> item ( $values [ "id" ] );
		if ( $m )
		{
			unset ( $values [ "id" ] );

			if ( $values [ "photo" ] -> error != 4 )
			{

		
				$gallery = $this -> galleryStorage -> getGalleryBy ( [ "name" => "Facebook" ] );
				if ( count ( $gallery ) != 1 )
				{
					$button -> form -> addError ( "Neexistuje jednoznačná galerie \"Facebook\"" );
					$this -> invalidateControl ();
					$this -> template -> edit_musician = 1;
					return;
				}
				$gallery = $gallery [ 0 ];

				$data = [ "content" => ucfirst ( $values [ "name" ] ) . " " . ucfirst ( $values [ "surname" ] ),
						  "gallery" => $gallery ];

				try{

					$response = $this -> galleryStorage -> upload ( $values [ "photo" ], $data );
				} catch ( \Exception $e )
				{
					$button -> form -> addError ( "Nepodařilo se nahrát fotku" );
					$this -> invalidateControl ();
					$this -> template -> edit_musician = 1;
					return;
				}


				try{

					$values [ "photo" ] = $response -> getImage ();

					$photo = $m -> photo;
					if ( $photo )
					{
						$m -> setPhoto ( NULL );
						$this -> galleryStorage -> delete ( $photo -> id );
					}
				} catch ( \Exception $e )
				{
					$button -> form -> addError ( "Nepodařilo se nahrát fotku" );
					$this -> template -> edit_musician = 1;
					$this -> invalidateControl ();
					return;			
				}

			}
			else
				unset ( $values [ "photo" ] );


			try{
				$this -> musicians -> update_array ( $m, $values );
				
			} catch ( \Exception $e )
			{
				$button -> form -> addError ( "Nepodařilo se uložit data" );
				$this -> template -> edit_musician = 1;
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
		$this -> template -> setFile ( __DIR__ . "/musicianControl.latte" );

		$this -> template -> musician = $this -> musician;
		$this -> template -> render ();
	}

}