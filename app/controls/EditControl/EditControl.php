<?php

namespace Controls;

use Tulinkry\Application\UI\Control;
use Tulinkry;
use Forms;
use Model;
use Nette;

class EditControl extends Control
{

	protected $galleryStorage;
	protected $photos;
	protected $galleries;
	protected $pres;

	public function __construct ( $presenter, Model\GalleryStorage $model, Model\PhotoModel $photos, Model\GalleryModel $galleries )
	{
		parent::__construct ();

		$this -> pres = $presenter;
		$this -> galleryStorage = $model;
		$this -> photos = $photos;
		$this -> galleries = $galleries;

	}


	protected function createComponentEditGrid ( $name )
	{
        $grid = new EditGrid ( $this -> galleryStorage );
        $photos = $this -> photos;
        $presenter = $this -> pres;
        $grid -> setDatasourceCallback(function($filter, $order, $paginator) use ( $presenter, $photos )
        {
            $filter = $filter + [ "parent" => NULL ];
            $order = [ "updated" => "DESC" ];

            if ( $paginator )
            {
	            return $photos -> limit ( $paginator -> itemsPerPage, $paginator -> offset, $filter, $order );
            }
            return $photos -> by ( $filter, $order );
        });

        $galleries = $this -> galleries;
        $grid -> setEditFormFactory(function($row) use ( $galleries ) {
            $form = new Tulinkry\Forms\Container;
            //$form->addDateTimePicker('created_time');
            $form->addHidden("id");
            $form->addText('content');
                //->setRequired();
                // set your own conditions

            $form -> addSelect ( "gallery", "Galerie", $galleries->all() );
            // set your own fileds, inputs

            if ($row) {
                $form->setDefaults($row->toArray());
            }
            return $form;
        });

        $grid -> setEditFormCallback ( $this -> saveData );

        $grid->setPagination( 10, function( $filter, $order ) use ( $photos )
    	{
            $filter = $filter + [ "parent" => NULL ];
    		return $photos -> count ( $filter );
    	} );

        return $this [ $name ] = $grid;
		//return $this [ $name ] = new Controls\EditControl ( $this -> context -> galleryStorage, $this -> context -> photos, $this -> context -> galleries );
	}

	public function saveData ( $form )
	{
		$values = $form -> values;
		if ( ( $item = $this -> photos -> item ( $values [ "id" ] ) ) )
		{
			//$item -> content = $values [ "content" ];
			//$item -> updated = new Nette\DateTime;

			$values = (array)$values + [ "updated" => new Nette\DateTime ];
			$this -> photos -> update_array ( $item, $values );

			return;

			$item -> gallery = $this -> galleries -> item ( $values [ "gallery" ] );
			$this -> photos -> update ( $item );
		}
	}


	public function render ()
	{	
		$this -> template -> setFile ( __DIR__  . "/editControl.latte" );

		$this -> template -> render ();
	}

}