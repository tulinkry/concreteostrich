<?php


namespace Controls;

use Tulinkry\Application\UI;
use Oli;


class EventControl extends UI\Control
{

	protected $model;
	protected $event;
	protected $mode = "default";

	public function __construct ( $model, $id )
	{
		$this -> model = $model;
		$this -> event = $model -> item ( $id );
	}

	public function setFile (  $mode = "default" )
	{
		$this -> mode = $mode;
	}

	public function render ()
	{
		$this -> template -> setFile ( __DIR__ . "/eventControl" . ucfirst ( $this -> mode ) . ".latte");

		$this -> template -> short = [];

		$args = func_get_args ();

		if ( is_array ( $args ) && count ( $args ) > 0 )
		{
			foreach ( $args as $key => $value ) 
			{
				if ( is_array ( $value ) )
					foreach ( $value as $k => $v )
						$this -> template -> short [ $k ] = $v;
			}
		}


		//$im = new \Imagick ( "D:\Obrázky\Roadtrip 2014\Foto" );

		if ( $this -> model && $this -> event && $this -> event -> id )
			$this -> template -> event = $this -> model -> item ( $this -> event -> id );
		$this -> template -> render ();
	}


	public function renderMap ()
	{
		$this -> template -> setFile ( __DIR__ . "/eventControlMap.latte");

		$this -> template -> short = [];

		$args = func_get_args ();

		if ( is_array ( $args ) && count ( $args ) > 0 )
		{
			foreach ( $args as $key => $value ) 
			{
				if ( is_array ( $value ) )
					foreach ( $value as $k => $v )
						$this -> template -> short [ $k ] = $v;
			}
		}


		//$im = new \Imagick ( "D:\Obrázky\Roadtrip 2014\Foto" );

		if ( $this -> model && $this -> event && $this -> event -> id )
			$this -> template -> event = $this -> model -> item ( $this -> event -> id );
		$this -> template -> render ();
	}

	protected function createComponentMap ()
	{
		$map = $this->presenter->map->create();

		$map->setProportions('100%', '500px');
		$map->setCoordinates(array($this->event->latitude, $this->event->longitude))
		    ->setZoom(15)
		    ->setType(Oli\GoogleAPI\MapAPI::ROADMAP);

		$markers = $this->presenter->markers->create();
		//$markers->fitBounds();

		$markers -> addMarker ( array ( $this->event->latitude, $this->event->longitude ), Oli\GoogleAPI\Markers::DROP )
				 -> setMessage ( $this->event->name );

		$map->addMarkers($markers);
		return $map;
	}


};
