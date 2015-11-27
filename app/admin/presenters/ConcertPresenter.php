<?php

namespace AdminModule\Presenters;

use AdminModule\Controls;
use Model;
use Nette\Application\UI\Multiplier;
use Tulinkry\Application\UI;
use Oli;
use Tulinkry;

class ConcertPresenter extends BasePresenter
{
	/** @inject @var Model\EventModel */
	public $events;

	/** @inject @var Model\FbImportModel */
	public $fb;

	/** @inject @var Oli\GoogleAPI\IMapAPI */
	public $map;

	/** @inject @var Oli\GoogleAPI\IMarkers */
	public $markers;

	/** @var int */
	protected $event_id;

	/** @var Model\GalleryStorage @inject */
	public $galleryStorage;

	const ARCHIVE_EVENT_NUMBER = 15;

	public function renderDefault ()
	{

		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemCount = $this -> context -> events -> count ( [ "hidden" => false ], [ "start" => "DESC", "end" => "DESC" ] );
	
		$this -> template -> concerts = $this -> context -> events -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [ "hidden" => false ], [ "start" => "DESC", "end" => "DESC" ] );
		//print_r ( $this -> template -> wallposts );
		//$this -> context -> events -> all ();
	}

	public function renderArchive ()
	{

		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemCount = $this -> context -> events -> count ( [], [ "start" => "DESC", "end" => "DESC" ] );
		$paginator -> itemsPerPage = self::ARCHIVE_EVENT_NUMBER;
		$this -> template -> concerts = $this -> context -> events -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [], [ "start" => "DESC", "end" => "DESC" ] );
		//print_r ( $this -> template -> wallposts );
		//$this -> context -> events -> all ();

	}



	protected function createComponentEvents ()
	{
		$model = $this -> events;
		return new Multiplier ( function ( $event_id ) use ( $model ) 
		{
			$control = new Controls\EventControl ( $model, $event_id );
			$control -> setFile ( $this -> action );
			return $control;
		});
	}

	public function actionEvent ( $id )
	{
		$this -> event_id = $id;
	}

	
	public function handleHideAll ()
	{
		$this -> setHiddenArchive ( true );
		$this -> invalidateControl ( "events" );
		$this -> invalidateControl ( "actions" );
	}

	public function handleUnHideAll ()
	{
		$this -> setHiddenArchive ( false );
		$this -> invalidateControl ( "events" );
		$this -> invalidateControl ( "actions" );
	}

	protected function setHiddenArchive ( $value )
	{
		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemsPerPage = self::ARCHIVE_EVENT_NUMBER;

		$events = $this -> context -> events -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [], [ "start" => "DESC", "end" => "DESC" ] );
		foreach ( $events as $event )
			$event -> hidden = $value;
		$this -> context -> events -> flush ();	
	}


	protected function createComponentEvent ( $name )
	{
		$control = new Controls\EventControl ( $this -> events, $this -> event_id );
		$control -> setFile ( "map" );
		return $this [ $name ] = $control;
	}

	protected function createComponentAddConcertForm ( $name )
	{
		$form = new UI\Form;

		$form -> addText ( "name", "Název" );
		$form -> addTextArea ( "content", "Popis" );

		$form -> addText ( "location", "Místo" );


		$form -> addDate ( "start", "Začátek" )
			  -> setMask ( "d. m. Y H:i:s" )
			  -> setDefaultValue ( new Tulinkry\DateTime );
		$form -> addDate ( "end", "Konec" )
			  -> setMask ( "d. m. Y H:i:s" )
			  -> linked ( $form [ "start" ] )
			  -> setDefaultValue ( new Tulinkry\DateTime );

		$form -> addText ( "latitude", "Souřadnice latitude" );
		$form -> addText ( "longitude", "Souřadnice longitude" );

		$form -> addUpload ( "cover", "Fotka" );



		$form -> addSubmit ( "submit", "Vložit" )
			  -> setAttribute ( "class", "aja" )
			  -> onClick [] = callback ( $this, "process" );

		$form -> onValidate [] = function ( $button )
		{
			if ( isset ( $button -> form -> values [ "start" ] ) && isset ( $button -> form -> values [ "end" ] ) )
			{
				if ( $button -> form -> values [ "start" ] > $button -> form -> values [ "end" ] )
				{
					$button -> form -> addError ( "Začátek události nesmí být později než konec" );
					$this -> template -> date_edit = 1;
					$this -> invalidateControl ();
				}
			}
		};

		return $this [ $name ] = $form;
	}

	public function process ( $button )
	{
		$values = $button -> form -> values;

		$gallery = $this -> galleryStorage -> getGalleryBy ( [ "name" => "Facebook" ] );
		if ( count ( $gallery ) != 1 )
		{
			$button -> form -> addError ( "Neexistuje jednoznačná galerie \"Facebook\"" );
			return;
		}
		$gallery = $gallery [ 0 ];

		$data = [ "content" => ucfirst ( $values [ "name" ] ),
				  "gallery" => $gallery -> getId () ];


		if ( isset ( $values [ "cover" ] ) && $values [ "cover" ] -> isOk () )
		{
			try{

				$response = $this -> galleryStorage -> upload ( $values [ "cover" ], $data );
			} catch ( \Exception $e )
			{
				$button -> form -> addError ( "Nepodařilo se nahrát fotku" );
				return;
			}
			$values [ "cover" ] = $response -> getImage ();
		}
		else
		{
			unset ( $values [ "cover" ] );
		}


		try{

			$musician = $this -> events -> create ( $values );
			$this -> events -> insert ( $musician );
		} catch ( \Exception $e )
		{
			$button -> form -> addError ( "Nepodařilo se vložit událost" );
			$button -> form -> addError ( $e -> getMessage () );
			return;
		}


		$this -> flashMessage ( "Událost vložena" );
		$this -> redirect ( "event", [ "id" => $musician -> getId () ] );

	}


}
