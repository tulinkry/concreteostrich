<?php

namespace AdminModule\Presenters;

use Nette,
	Model,
	Entity;
use Nette\Application\UI\Multiplier;
use AdminModule\Controls;
use Tulinkry\Application\UI;
use Tulinkry;

/**
 * Homepage presenter.
 */
class IndexPresenter extends BasePresenter
{

	/** @inject @var Model\PostModel */
	public $posts;

	/** @var Model\GalleryStorage @inject */
	public $galleryStorage;

	const ARCHIVE_EVENT_NUMBER = 15;

	public function renderDefault ()
	{
		$this -> template -> wallposts = [];//$this -> context -> fb -> getAllPosts ();
		//print_r ( $this -> template -> wallposts );
		$paginator = $this [ "paginator" ] -> getPaginator ();

		$by = [ "hidden" => false ];

		$paginator -> itemCount = $this -> context -> posts -> count ( $by, [ "datum" => "DESC" ]  );
		$this -> template -> posts = $this -> context -> posts -> limit ( $paginator -> itemsPerPage, $paginator -> offset, $by, [ "datum" => "DESC" ] );
	}

	public function renderArchive ()
	{
		$this -> template -> wallposts = [];//$this -> context -> fb -> getAllPosts ();
		//print_r ( $this -> template -> wallposts );
		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemsPerPage = self::ARCHIVE_EVENT_NUMBER;
		$paginator -> itemCount = $this -> context -> posts -> count ( [], [ "datum" => "DESC" ]  );
		$this -> template -> posts = $this -> context -> posts -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [], [ "datum" => "DESC" ] );
	}

	protected function createComponentPosts ()
	{
		$model = $this -> context -> posts;
		$mode = $this -> action;
		return new Multiplier ( function ( $some_id ) use ( $model, $mode )
		{
			$control = new Controls\PostControl ( $model, $some_id );
			$control -> setFile ( $mode );
			return $control;
		});
	}

	public function handleHideAll ()
	{
		$this -> setHiddenArchive ( true );
		$this -> invalidateControl ( "posts" );
	}

	public function handleUnHideAll ()
	{
		$this -> setHiddenArchive ( false );
		$this -> invalidateControl ( "posts" );
	}

	protected function setHiddenArchive ( $value )
	{
		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemsPerPage = self::ARCHIVE_EVENT_NUMBER;

		$posts = $this -> context -> posts -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [], [ "datum" => "DESC" ] );
		foreach ( $posts as $event )
			$event -> hidden = $value;
		$this -> context -> posts -> flush ();	
	}


	protected function createComponentAddPostForm ( $name )
	{
		$form = new UI\Form;

		$form -> addText ( "name", "Název" );

		$form -> addSelect ( "type", "Typ", Entity\Post::$types )
			  -> setDefaultValue ( Entity\Post::TYPE_STATUS );

		$form [ "type" ] -> addCondition ( UI\Form::EQUAL, Entity\Post::TYPE_PHOTO ) -> toggle ( "add-post-image" );
		$form [ "type" ] -> addCondition ( UI\Form::EQUAL, Entity\Post::TYPE_VIDEO ) -> toggle ( "add-post-link" );
		$form [ "type" ] -> addCondition ( UI\Form::EQUAL, Entity\Post::TYPE_LINK ) -> toggle ( "add-post-link" );

		$form -> addDate ( "datum", "Datum" )
			  -> setMask ( "d. m. Y H:i:s" )
			  -> setDefaultValue ( new Tulinkry\DateTime );


		$form -> addUpload ( "image", "Fotka" )
			  -> setOption ( 'id', 'add-post-image' )
			  -> addConditionOn ( $form["type"], UI\Form::EQUAL, Entity\Post::TYPE_PHOTO )
			  	-> setRequired ( "Pro nahrání fotky, musíte přidat fotku." );


		$form -> addText ( "link", "Odkaz" )
			  -> setOption ( 'id', 'add-post-link' )
			  -> addConditionOn ( $form["type"], UI\Form::EQUAL, Entity\Post::TYPE_LINK )
			  	-> setRequired ( "Pro vložení odkazu musíte přidat odkaz." );

		$form [ "link"] -> addConditionOn ( $form["type"], UI\Form::EQUAL, Entity\Post::TYPE_VIDEO )
			  				-> setRequired ( "Pro vložení videa musíte přidat odkaz." );

		$form -> addTextArea ( "message", "Obsah" )
			  -> setOption ( 'id', 'add-post-text' )
			  -> addConditionOn ( $form["type"], UI\Form::EQUAL, Entity\Post::TYPE_STATUS )
			  	-> setRequired ( "Pro typ text musíte napsat nějaký text" );


		$form -> addSubmit ( "submit", "Vložit" )
			  -> setAttribute ( "class", "aja" );

		return $this [ $name ] = $form;
	}

	public function process ( $button )
	{
		$values = $button -> form -> values;


		switch ( $values [ "type" ] )
		{
			case Entity\Post::TYPE_PHOTO:
				$gallery = $this -> galleryStorage -> getGalleryBy ( [ "name" => "Facebook" ] );
				if ( count ( $gallery ) != 1 )
				{
					$button -> form -> addError ( "Neexistuje jednoznačná galerie \"Facebook\"" );
					return;
				}
				$gallery = $gallery [ 0 ];

				$data = [ "content" => ucfirst ( $values [ "name" ] ),
						  "gallery" => $gallery -> getId () ];


				if ( isset ( $values [ "image" ] ) && $values [ "image" ] -> isOk () )
				{
					try{

						$response = $this -> galleryStorage -> upload ( $values [ "image" ], $data );
					} catch ( \Exception $e )
					{
						$button -> form -> addError ( "Nepodařilo se nahrát fotku" );
						return;
					}
					$values [ "image" ] = $response -> getImage ();
				}
				else
				{
					unset ( $values [ "image" ] );
				}
			break;
			case Entity\Post::TYPE_VIDEO:
				unset ( $values [ "image" ] );
			break;
			case Entity\Post::TYPE_LINK:
				unset ( $values [ "image" ] );
			break;
			case Entity\Post::TYPE_STATUS:
				unset ( $values [ "image" ] );
			break;
			default:
				$form -> addError ( "Neznámý typ příspěvku." );
				return;
		}



		try{

			$post = $this -> posts -> create ( $values );
			$this -> posts -> insert ( $post );
		} catch ( \Exception $e )
		{
			$button -> form -> addError ( "Nepodařilo se vložit příspěvek" );
			$button -> form -> addError ( $e -> getMessage () );
			return;
		}


		$this -> flashMessage ( "Příspěvek vložen" );
		$this -> redirect ( "default" );

	}



}
