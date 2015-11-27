<?php


namespace AdminModule\Controls;

use Tulinkry\Application\UI;
use Controls;
use Oli;

class EventControl extends Controls\EventControl
{

	protected $model;
	protected $event;


	public function __construct ( $model, $id )
	{
		$this -> model = $model;
		$this -> event = $model -> item ( $id );
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

	public function renderMail ()
	{
		$this -> template -> setFile ( __DIR__ . "/eventControlMail.latte");

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

	public function handleDelete ()
	{
		try{
			$this -> model -> remove ( $this -> event );
			$this -> presenter -> flashMessage ( "Událost smazána" );
			$this -> event = null;
		} catch ( \Exception $e )
		{
			$this -> presenter -> flashMessage ( "Událost se nepodařilo smazat." );
		}
		$this -> invalidateControl ();
		$this -> presenter -> invalidateControl ( "flashMessageArea" );
		$this -> presenter -> invalidateControl ( "events" );
	}

	public function handleContentEdit ()
	{
		$this -> template -> content_edit = 1;
		$this -> template -> visible = 1;
		$this -> invalidateControl ();
	}

	public function handleHide ()
	{
		//$this -> presenter -> flashMessage ( "Událost se nepodařilo smazat.", "success");
		//print_r ( $this -> presenter -> getFlashSession( ) );
		//throw new \Exception ( "Bohužel nejde schovávat tuto událost");
		$this -> event -> hidden = 1;
		$this -> model -> update ( $this -> event );
		$this -> presenter -> invalidateControl ( "events" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );	
		$this -> presenter -> invalidateControl ( "actions" );	
		$this -> invalidateControl ();
	}

	public function handleUnhide ()
	{
		$this -> event -> hidden = 0;
		$this -> model -> update ( $this -> event );
		$this -> presenter -> invalidateControl ( "events" );
		$this -> presenter -> invalidateControl ( "flashMessageArea" );	
		$this -> presenter -> invalidateControl ( "actions" );	
		$this -> invalidateControl ();
	}

	public function handleDateEdit ()
	{
		$this -> template -> date_edit = 1;
		$this -> invalidateControl ();
	}

	public function handleLocationEdit ()
	{
		$this -> template -> location_edit = 1;
		$this -> invalidateControl ();
	}

	public function handleNameEdit ()
	{
		$this -> template -> name_edit = 1;
		$this -> invalidateControl ();
	}


	protected function createComponentDateForm ()
	{
		$form = new UI\Form;

		//$form -> addDateTime ( "start" );
		//$form -> addDateTime ( "end" );
		$form -> addDate ( "start" )
			  -> setMask ( "d. m. Y H:i:s" );
		$form -> addDate ( "end" )
			  -> setMask ( "d. m. Y H:i:s" );
		$form -> addSubmit ( "submit", "Uložit" )
			-> setAttribute ( "class", "ajax" );

		$form -> addHidden ( "id" );

		$form -> setDefaults ( $this -> event -> toArray () );

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

		return $this [ "dateForm" ] = $form;
	}

	protected function createComponentLocationForm ()
	{
		$form = new UI\Form;

		$form -> addText ( "location" );
		$form -> addText ( "latitude" );
		$form -> addText ( "longitude" );
		$form -> addSubmit ( "submit", "Uložit" )
			-> setAttribute ( "class", "ajax" );

		$form -> addHidden ( "id" );

		$form -> setDefaults ( $this -> event -> toArray () );


		return $this [ "locationForm" ] = $form;
	}

	protected function createComponentContentForm ()
	{
		$form = new UI\Form;

		$form -> addTextArea ( "content" );
		$form -> addSubmit ( "submit_content", "Uložit" )
			-> setAttribute ( "class", "ajax" );

		$form -> addHidden ( "id" );

		$form -> setDefaults ( $this -> event -> toArray () );

		return $this [ "contentForm" ] = $form;
	}

	protected function createComponentNameForm ()
	{
		$form = new UI\Form;

		$form -> addText ( "name" );
		$form -> addSubmit ( "submit", "Uložit" )
			-> setAttribute ( "class", "ajax" );

		$form -> addHidden ( "id" );

		$form -> setDefaults ( $this -> event -> toArray () );

		return $this [ "nameForm" ] = $form;
	}

	public function process ( $button )
	{
		$values = $button -> form -> getValues ( TRUE );

		$event = $this -> model -> item ( $values [ "id" ] );

		if ( $this["contentForm"] -> isSubmitted () )
			$this -> template -> visible = 1;

		if ( $event )
		{
			unset ( $values [ "id" ] );

			$this -> model -> update_array ( $event, $values );
			$this -> presenter -> flashMessage ( "Změny uloženy." );
			if ( ! $this -> isAjax () )
				$this -> redirect ( "this" );
			$this -> invalidateControl ();
		}
		else
		{
			$this -> presenter -> flashMessage ( "Nastala chyba. Nenalezena událost." );
			if ( ! $this -> isAjax () )
				$this -> redirect ( "this" );
			$this -> invalidateControl ();

		}


	}


};
