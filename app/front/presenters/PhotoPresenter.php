<?php

namespace FrontModule\Presenters;

use Nette,
	Model,
	Forms;
use Tulinkry\Application\UI\Form;
use Tulinkry;
use Controls;
use Nextras;

/**
 * Homepage presenter.
 */
class PhotoPresenter extends BasePresenter
{

    public function renderDefault ()
    {

    }


    protected function createComponentGallery ( $name )
    {
        $control = new Tulinkry\Gallery\GalleryControl ( $this -> context -> galleryStorage );
        return $this [ $name ] = $control;
    }

    public function renderUpload ()
    {
        $this [ "upload" ] -> addTemplateVariables ( [ "galleries" => $this -> context -> galleries -> by ( [], [ "datum" => "DESC" ] ) ] );
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
