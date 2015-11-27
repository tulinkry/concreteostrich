<?php

namespace AdminModule\Presenters;

use Nette,
	Model,
	Forms;

use Tulinkry\Application\UI\Form;
use Tulinkry;
use AdminModule;
use Controls;
use Nextras;
use Nette\Application\UI\Multiplier;

/**
 * Homepage presenter.
 */
class PhotoPresenter extends BasePresenter
{

    public function renderDefault ()
    {

    }

    public function renderUpload ()
    {
        $this [ "upload" ] -> addTemplateVariables ( [ "galleries" => $this -> context -> galleries -> by ( [], [ "datum" => "DESC" ] ) ] );
    }


    public function renderGallery ()
    {
        $this -> template -> galleries = $this -> context -> galleries -> all ();
    }

    public function renderGalleryInsert ()
    {

    }



    protected function createComponentAddGalleryForm ( $name )
    {
        $form = new AdminModule\Controls\GalleryForm;

        $form -> addSubmit ( "submit", "Vložit" )
              -> onClick [] = callback ( $this, "insert" );

        return $form;
    }
    
    public function insert ( $button )
    {
        
        $values = $button -> form -> values;
        try{
            unset ( $values [ "id" ] );
            $gallery = $this -> context -> galleries -> create ( $values );
            $this -> context -> galleries -> insert ( $gallery );
        } catch ( \Exception $e )
        {
            $button -> form -> addError ( "Nepodařilo se vložit galerii." );
            $button -> form -> addError ( $e -> getMessage () );
            return;
        }


        $this -> flashMessage ( "Galerie vložena" );
        $this -> redirect ( "gallery" );        
    }

    protected function createComponentGalleries ()
    {
        $model = $this -> context -> galleries;
        $storage = $this -> context -> galleryStorage;
        return new Multiplier ( function ( $some_id ) use ( $model, $storage )
        {
            $control = new AdminModule\Controls\GalleryControl ( $storage, $model, $some_id );
            return $control;
        });
    }    

    protected function createComponentGallery ( $name )
    {
        $control = new Tulinkry\Gallery\GalleryControl ( $this -> context -> galleryStorage );
        return $this [ $name ] = $control;
    }



    /**
     * Gallery grid
     */
    protected function createComponentEditGrid()
    {
        return null;
        return new \EditGrid($this->context->photos);
    }


    protected function createComponentUpload ( $name )
    {
        $control = new \Tulinkry\UploadControl;
        $presenter = $this;
        $control -> onUpload [] = $this -> context -> galleryStorage -> upload;
        $control -> onUpload [] = function () use ( $presenter ) { $presenter -> invalidateControl ( "files" ); };
        $control -> addTemplate ( __DIR__ . "/../templates/Photo/upload/upload.latte" );


        return $this [ $name ] = $control;
    }

	protected function createComponentEditControl ( $name )
	{
		return $this [ $name ] = new Controls\EditControl ( $this, $this -> context -> galleryStorage, $this -> context -> photos, $this -> context -> galleries );
	}
}
