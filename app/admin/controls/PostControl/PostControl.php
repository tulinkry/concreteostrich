<?php

namespace AdminModule\Controls;

use Tulinkry\Application\UI;
use Controls;

class PostControl extends Controls\PostControl
{

	protected $posts;
	protected $post;
	protected $mode = "default";

	public function __construct ( $model, $id )
	{
		$this -> posts = $model;
		$this -> post = $model -> item ( $id );
	}

	public function setFile (  $mode = "default" )
	{
		$this -> mode = $mode;
	}

	public function handleEdit ()
	{
		$this -> template -> edit = 1;
		$this -> invalidateControl ();
	}


	public function handleDelete ()
	{
		try{
			$this -> posts -> remove ( $this -> post );
			$this -> presenter -> flashMessage ( "Příspěvek smazán" );
			$this -> post = null;
		} catch ( \Exception $e )
		{
			$this -> presenter -> flashMessage ( "Příspěvek se nepodařilo smazat.", "error" );
		}
		$this -> presenter -> invalidateControl ( "posts" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );
		$this -> invalidateControl ();
	}

	public function handleHide ()
	{
		$this -> post -> hidden = 1;
		$this -> posts -> update ( $this -> post );
		$this -> presenter -> invalidateControl ( "posts" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );		
		$this -> invalidateControl ();
	}

	public function handleUnhide ()
	{
		$this -> post -> hidden = 0;
		$this -> posts -> update ( $this -> post );
		$this -> presenter -> invalidateControl ( "posts" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );		
		$this -> invalidateControl ();
	}

	protected function createComponentEditPostForm ()
	{
		$form = new UI\Form;

		$form -> addText ( "name", "Název" );

		$form -> addTextArea ( "message", "Obsah" );

		$form -> addDate ( "datum", "Datum" )
			  -> setMask ( "d. m. Y H:i" );

		$form -> addUpload ( "image", "Obrázek" );

		$form -> addHidden ( "id" );

		$form -> addSubmit ( "submit", "Odeslat" )
			  -> setAttribute ( "class", "ajax" );

		$form -> setDefaults ( $this -> post -> toArray () );

		return $form;
	}

	public function render ()
	{
		$this -> template -> setFile ( __DIR__ . "/postControl" . ucfirst ( $this -> mode ) . ".latte");

		$args = func_get_args();

		$this -> template -> short = [];

		if ( is_array ( $args ) && count ( $args ) > 0 )
		{
			foreach ( $args as $key => $value ) 
			{
				if ( is_array ( $value ) )
					foreach ( $value as $k => $v )
						$this -> template -> short [ $k ] = $v;
			}
		}


		$this -> template -> post = $this -> post;

		$this -> template -> render ();
	}

	public function process ( $button )
	{
		$values = $button -> form -> getValues(TRUE);
		$m = $this -> posts -> item ( $values [ "id" ] );
		if ( $m )
		{
			unset ( $values [ "id" ] );

			if ( $values [ "image" ] -> error != 4 )
			{

		
				$gallery = $this -> galleryStorage -> getGalleryBy ( [ "name" => "Facebook" ] );
				if ( count ( $gallery ) != 1 )
				{
					$button -> form -> addError ( "Neexistuje jednoznačná galerie \"Facebook\"" );
					$this -> invalidateControl ();
					$this -> template -> edit = 1;
					return;
				}
				$gallery = $gallery [ 0 ];

				$data = [ "content" => ucfirst ( $values [ "name" ] ),
						  "gallery" => $gallery ];

				try{

					$response = $this -> galleryStorage -> upload ( $values [ "image" ], $data );
				} catch ( \Exception $e )
				{
					$button -> form -> addError ( "Nepodařilo se nahrát fotku" );
					$this -> invalidateControl ();
					$this -> template -> edit = 1;
					return;
				}


				try{

					$values [ "image" ] = $response -> getImage ();

					$photo = $m -> image;
					if ( $photo )
					{
						$m -> setPhoto ( NULL );
						$this -> galleryStorage -> delete ( $photo -> id );
					}
				} catch ( \Exception $e )
				{
					$button -> form -> addError ( "Nepodařilo se nahrát fotku" );
					$this -> template -> edit = 1;
					$this -> invalidateControl ();
					return;			
				}

			}
			else
				unset ( $values [ "image" ] );


			try{
				$this -> posts -> update_array ( $m, $values );
				
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

};