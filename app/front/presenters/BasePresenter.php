<?php

namespace FrontModule\Presenters;

use Nette;
use Tulinkry\Application\UI;
use Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends UI\Presenter
{

	/** @var Model\EventModel @inject */
	public $events;

	public function startup ()
	{
		parent::startup ();
    
	    if ( ! $this -> reflection -> hasAnnotation ( "Cron" ) )
	    {
	  		$session = $this -> getSession ( "welcomePageViewed" );
	  		if ( ! $this -> getSession () -> hasSection ( "welcomePageViewed" ) || ! $session -> viewed )
	  		{
	  			$session -> viewed = true;    
	  			$this -> redirect ( ":Front:Index:welcome" );
	  		}
	  		//if ( $this -> getSession () -> hasSection ( "welcomePageViewed" ) )
	  		//	$session -> viewed = false;
	    }
	}

	public function beforeRender ()
	{
		parent::beforeRender ();
		$this -> template -> nextActions = $this -> events -> next (); 
		$this -> template -> pastActions = $this -> events -> past (); 
	}

}
