<?php

namespace FrontModule\Presenters;

use Model;
use Controls;
use Nette\Application\UI\Multiplier;
use Oli;


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

	/** @var integer */
	protected $event_id;

	public function renderDefault ()
	{

		$paginator = $this [ "paginator" ] -> getPaginator ();
		$paginator -> itemCount = $this -> context -> events -> count ( [ "hidden" => false ], [ "start" => "DESC", "end" => "DESC" ] );
	
		$this -> template -> concerts = $this -> context -> events -> limit ( $paginator -> itemsPerPage, $paginator -> offset, [ "hidden" => false ], [ "start" => "DESC", "end" => "DESC" ] );
		//print_r ( $this -> template -> wallposts );
		//$this -> context -> events -> all ();

	}


	protected function createComponentEvents ()
	{
		$model = $this -> events;
		return new Multiplier ( function ( $event_id ) use ( $model ) 
		{
			$control = new Controls\EventControl ( $model, $event_id );
			return $control;
		});
	}

	public function actionEvent ( $id )
	{
		$this -> event_id = $id;
	}

	protected function createComponentEvent ( $name )
	{
		$control = new Controls\EventControl ( $this -> events, $this -> event_id );
		$control -> setFile ( "map" );
		return $this [ $name ] = $control;
	}

}
