<?php

namespace Tulinkry\Forms;

use Nette;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\MultiSelectBox;

class Form extends Nette\Application\UI\Form
{

	//protected $translator;


	protected function attached($presenter)
	{
		parent::attached($presenter);

		if ($presenter -> context -> hasService ( "translator" ) )
		{ 
			$this -> translator = $presenter -> context -> translator;
			$this -> setTranslator ( $this->translator );
		}

		if ($presenter instanceof Nette\Application\IPresenter) 
		{
			$this->attachHandlers($presenter);
		}

		if ($presenter instanceof Nette\Application\IPresenter) 
		{
			$this->attachClasses($presenter);
		}

	}


	protected function attachClasses ( $presenter )
	{
		foreach ( $this -> getComponents ( TRUE ) as $cmp )
			if ( $cmp instanceof SelectBox || 
				 $cmp instanceof MultiSelectBox ||
				 $cmp instanceof Controls\SelectBox )
			{
				$cmp -> getControlPrototype() -> addClass ( "selectpicker form-control" );
				$cmp -> setAttribute ( "data-live-search", "true" );
			}
		$renderer = $this -> getRenderer ();
		$renderer -> wrappers [ 'controls' ] [ 'container' ] = "table class=\"table table-condensed\"";
		$renderer -> wrappers [ 'control' ] [ '.text' ] = "form-control";
		$renderer -> wrappers [ 'control' ] [ '.password' ] = "form-control";
		$renderer -> wrappers [ 'control' ] [ '.submit' ] = "form-control btn btn-primary";
	}
/*
	public function __construct ( $parent = NULL, $name = NULL )
	{
		parent::__construct ( $parent, $name );

		//$this -> setRenderer ( new BootstrapRenderer );
		$renderer = $this -> getRenderer ();
		$renderer -> wrappers [ 'controls' ] [ 'container' ] = "table class=\"table-condensed\"";
		$renderer -> wrappers [ 'control' ] [ '.text' ] = "form-control";
		$renderer -> wrappers [ 'control' ] [ '.password' ] = "form-control";
		$renderer -> wrappers [ 'control' ] [ '.submit' ] = "form-control btn";

	}

	public function addSelect( $name, $label = NULL, array $items = NULL, $size = NULL )
	{
		return $this[$name] = new SelectBox($label, $items, $size);
	}



*/
	public function render ()
	{
        $args = func_get_args();
		if ( count ( $args ) > 0 )
		{
			if ( $args [ 0 ] == "horizontal" )
			{
				$renderer = $this -> getRenderer ();
				$renderer -> wrappers [ 'controls' ] [ 'container' ] = "ul class=\"form-inline\"";
				array_shift ( $args );
			}
			elseif ( $args [ 0 ] == "vertical" )
			{
				array_shift ( $args );
			}
		}

        array_unshift($args, $this);
        echo call_user_func_array(array($this->getRenderer(), 'render'), $args);
	}



	public function addContainer ( $name )
	{
	    $control = new Container;
	    $control -> currentGroup = $this -> currentGroup;
	    return $this[ $name ] = $control;
	}


	public function addSelect( $name, $label = NULL, array $items = NULL, $size = NULL )
	{
		return $this[$name] = new Controls\SelectBox($label, $items, $size);
	}

	public function addDate ( $name, $label = NULL, $cols = NULL, $maxLength = NULL )
	{
		return $this[$name] = new Controls\DateInput ( $label, $cols, $maxLength );
	}

	public function addEmail($name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$item = $this->addText($name, $label, $cols, $maxLength);
		$item->addCondition(self::FILLED)
			->addRule(self::EMAIL, "Email nemá správný formát.");

		return $item;
	}	

	public function addTextArea ( $name, $label = NULL, $cols = 40, $rows = 10 )
	{
		return $this[$name] = new Controls\TextArea ( $label, $cols, $rows );
	}

	protected function attachHandlers($presenter)
	{
		// $formNameSent = lcfirst($this->getName())."Sent";
		$formNameSent = "process" . lcfirst ( $this -> getName() );

		$possibleMethods = array(
			array( $presenter, $formNameSent ),
			array( $this -> parent, $formNameSent ),
			array( $this, "process" ),
			array( $this -> parent, "process")
		);

		foreach ( $possibleMethods as $method ) 
		{
			if ( method_exists( $method[0], $method[1] ) )
			{
				$this -> onSuccess[] = callback( $method[0], $method[1] );
			}
		}
	}

}