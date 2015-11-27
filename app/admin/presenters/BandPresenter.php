<?php

namespace AdminModule\Presenters;

use Nette,
	Model,
	AdminModule;

use Tracy\Dumper;
use Tulinkry;
use Tulinkry\Application\UI;
use Nette\Application\UI\Multiplier;
/**
 * Homepage presenter.
 */
class BandPresenter extends BasePresenter
{
	/** @var Model\MusicianModel @inject */
	public $musicians;
	/** @var Model\GalleryStorage @inject */
	public $galleryStorage;


	public function renderDefault ()
	{
		$this -> template -> descriptions = $this -> context -> musicians -> by ( [], [ "surname" => "DESC", "name" => "DESC" ] );
	}


	protected function createComponentMusicianControl ()
	{
		$g = $this -> galleryStorage;
		$m = $this -> musicians;
		return new Multiplier ( function ( $id ) use ( $g, $m ) 
		{
			$control = new AdminModule\Controls\MusicianControl ( $g, $m, $id );
			return $control;
		});
	}


	protected function createComponentAddMusicianForm ( $name )
	{
		$form = new UI\Form;

		$form -> addText ( "name", "Jméno" );
		$form -> addText ( "surname", "Příjmení" );
		$form -> addTextArea ( "content", "Popis" );


		$form -> addUpload ( "photo", "Fotka" );

		$form -> addSubmit ( "submit", "Vložit" )
			  -> setAttribute ( "class", "aja" )
			  -> onClick [] = callback ( $this, "process" );

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

		$data = [ "content" => ucfirst ( $values [ "name" ] ) . " " . ucfirst ( $values [ "surname" ] ),
				  "gallery" => $gallery -> id ];

		try{

			$response = $this -> galleryStorage -> upload ( $values [ "photo" ], $data );
		} catch ( \Exception $e )
		{
			$button -> form -> addError ( "Nepodařilo se nahrát fotku" . $e -> getMessage () );
			return;
		}


		try{

			$values [ "photo" ] = $response -> getImage ();
			$musician = $this -> musicians -> create ( $values );
			$this -> musicians -> insert ( $musician );
		} catch ( \Exception $e )
		{
			$button -> form -> addError ( "Nepodařilo se vložit hudebníka" );
			return;
		}


		$this -> flashMessage ( "Hudebník vložen" );
		$this -> redirect ( "default" );

	}


}
