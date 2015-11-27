<?php

namespace Model;

use Tulinkry;
use Entity;
use Tulinkry\Gallery;
use Tulinkry\Gallery\Image;
use Tulinkry\Model\BaseModel;

use Nette\Http\FileUpload;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Nette;
use Exception;

class GalleryStorage extends \Nette\Object
{
	protected $parameres;
	protected $galleries;
	protected $photos;

	protected $path = "";
	protected $image_path = "";
	protected $thumbnail_path;

	protected $url;
	protected $thumbnail_url;


	protected $gd_function_suffix = array(      
	  'image/pjpeg' => 'JPEG',     
	  'image/jpeg' => 'JPEG',     
	  'image/gif' => 'GIF',     
	  'image/bmp' => 'WBMP',     
	  'image/x-png' => 'PNG'     
	);	


	public function getPath ()
	{
		return $this -> path;
	}

	public function getThumbnails ()
	{
		return $this -> thumbnail_path;
	}

	public function __construct ( Nette\Application\Application $application, Tulinkry\Services\ParameterService $pservice, GalleryModel $gmodel, PhotoModel $pmodel  )
	{
		$this -> parameres = $pservice;
		$this -> galleries = $gmodel;
		$this -> photos = $pmodel;

		$this -> url = $pservice -> params [ "gallery" ] [ "path" ];
		$this -> path = WWW_DIR . "/" . $pservice -> params [ "gallery" ] [ "path" ];

		if (!is_dir($this -> path)) {
			umask(0);
			mkdir($this -> path, 0777);
		}

		$this -> thumbnail_url = $pservice -> params [ "gallery" ] [ "thumbnail_path" ];
		$this -> thumbnail_path = WWW_DIR . "/" . $pservice -> params [ "gallery" ] [ "thumbnail_path" ];
		if (!is_dir($this -> thumbnail_path)) {
			umask(0);
			mkdir($this -> thumbnail_path, 0777);
		}

	}

	public function getGalleries ()
	{
		return $this -> galleries -> all ();
	}

	public function getGalleryBy ( $criteria = [] )
	{
		return $this -> galleries -> by ( $criteria );
	}


	public function generateThumbnail ( $photo )
	{
		return "#error";
	}

	public function getImages ( $limit = 50, $offset = 0 )
	{
		$result = $this -> galleries -> loadPhotos ( $limit, $offset );
		$res = [];
        foreach ( $result as $item )
        {
        	if ( $item instanceof Entity\Gallery )
        	{
        		$images = [];
        		/*foreach ( $item -> photos as $image )
        		{
        			if ( $image -> parent )
        				continue;
        			if ( count ( $image -> thumbnails ) < 1 )
        				$thumbnail = $this -> generateThumbnail ( $image );
        			else
        				$thumbnail = $image -> thumbnails [ 0 ] -> url;
        			$images [] = new Tulinkry\Gallery\Photo ( $image->id, $image->content, $image->url, $thumbnail, $image->updated );
        		}*/
        		if ( count ( $images ) )
        		{
	        	}
        		$res [] = new Tulinkry\Gallery\Gallery ( $item->name, $item->content, $item->datum, [] );
        	}
        	else
        	{
    			if ( $item -> parent )
    				continue;
    			if ( count ( $item -> thumbnails ) < 1 )
    				$thumbnail = $this -> generateThumbnail ( $item );
    			else
    				$thumbnail = $item -> thumbnails [ 0 ] -> url;
        		$res [] = new Tulinkry\Gallery\Photo ( $item->id, $item->content, $item->url, $thumbnail, $item->updated );
        	}
        }

        return $res;

	}

	public function findImage ( $id )
	{
		return $this -> photos -> item ( $id );
	}
	public function removeImage ( $image )
	{
		return $this -> photos -> remove ( $image );
	}

	public function getImage ( $id )
	{
		if ( ! ( $photo = $this -> photos -> item ( $id ) ) )
			return NULL;

		$path = $photo -> getPath ();


		return new Gallery\Image ( $path );
	}

	public function getThumbnail ( $id, $width, $height )
	{
		if ( ! ( $photo = $this -> photos -> item ( $id ) ) )
			return NULL;

		$path = $photo -> getPath ();
		$image = new Gallery\Image ( $path );
		return $image -> getThumbnail ( $width, $height );
	}

	public function insert ( Entity\Photo $photo, $image )
	{
		$operation = "insert";
		if ( $this -> photos -> item ( $photo -> getId () ) )
			$operation = "update";
		
		// insert into file

		$photo -> setPath ( $path );

		return $this -> photos -> $operation ( $photo );
	}


	public function rotate ( $id, $angle = 90 )
	{
		if ( ( $photo = $this -> photos -> item ( $id ) ) )
		{
			$photo -> updated = new Nette\DateTime;
			$img = Image::fromFile ( $photo -> path );
			$img -> rotate ( -$angle, 0 );
			$img -> save ( $photo -> path );
			foreach ( $photo -> thumbnails as $thumbnail )
			{
				$img = Image::fromFile ( $thumbnail -> path );
				$img -> rotate ( -$angle, 0 );
				$img -> save ( $thumbnail -> path );
			}
			$this -> photos -> update ( $photo );
		}
	}

	public function delete ( $id )
	{
		if ( ! ( $file = $this -> photos -> item ( $id ) ) )
			return;

		// copy file

		$copy = $file;

		// copy thumbnails
        
        $thumbnails = $copy -> thumbnails -> toArray ();

	    // mark the file as deleted [thumbnails cascade = all]

        $this -> photos -> remove ( $file, BaseModel::NO_FLUSH );

 		// and try to delete them in one transaction

		$this -> photos -> flush ();

		// if successful cleanup the paths

		if( @file_exists($copy -> path) )
            @unlink($copy -> path);
        
        foreach ( $thumbnails as $thumbnail )
			if( @file_exists($thumbnail -> path) ) 
	            @unlink($thumbnail -> path);

	}


	public function fixImageRotation ( $path )
	{
		$exif = @exif_read_data($path);
		if (isset($exif['Orientation']))
		{
		  switch ($exif['Orientation'])
		  {
		    case 3:
				$img = Image::fromFile ( $path );
				$img -> rotate ( 180, 0 );
				$img -> save ( $path );
		      break;

		    case 6:
		      // Need to rotate 90 deg clockwise
				$img = Image::fromFile ( $path );
				$img -> rotate ( -90, 0 );
				$img -> save ( $path );
		      break;

		    case 8:
		      // Need to rotate 90 deg counter clockwise
				$img = Image::fromFile ( $path );
				$img -> rotate ( 90, 0 );
				$img -> save ( $path );
		      break;
		  }
		}	
	}

	public function upload(FileUpload $file, $additional_data)
	{
		if (!$file->isOk() || !$file->isImage()) {
			throw new Nette\InvalidArgumentException;
		}

		do {
			$name = Strings::random(10) . '_' . $file->getSanitizedName();
		} while (file_exists($path = $this->path . "/" . $name));
		

		$data = array_merge ( $additional_data, 
								[ "url" => $this -> url . "/" . $name, 
								  "path" => $this -> path . "/" . $name ] );
		
		//print_r ((int)Validators::is ( $data [ "gallery" ], "int:0.."));
		if ( ! array_key_exists ( "gallery", $data ) ||
			 ! array_key_exists ( "content", $data ) )
			throw new Exception("Nebyla vyplněna galerie nebo obsah!");

		if ( ! Validators::is ( intval ( $data [ "gallery" ] ), "int:1..") )
			throw new Exception( "Nebyla vyplněna galerie!" );
		
		$file->move($path);

		$this -> fixImageRotation ( $path );

		$photo = $this -> photos -> create ( $data );

        $img = Image::fromFile($path);

        for ( $i = 1; $i <= 1; $i ++ )
        {	
        	$img -> resize ( "300px", null )->save( $this -> thumbnail_path . '/thumbnail_' . ($i*80) . "x" . ($i*80) . "_" . $name);
        	$thb = array_merge ( $additional_data, [ "url" => $this -> thumbnail_url . '/thumbnail_' . ($i*80) . "x" . ($i*80) . "_" . $name,
        											 "path" => $this -> thumbnail_path . '/thumbnail_' . ($i*80) . "x" . ($i*80) . "_" . $name
        											] );
        	unset ( $thb [ "gallery" ] );
        	$thumbnail = $this -> photos -> create ( $thb );
        	$photo -> addThumbnail ( $thumbnail );
        }
        try{
			$this -> photos -> insert ( $photo );
		}
		catch ( \Exception $e )
		{
			$this -> cleanup ( $photo );
			throw $e;
		}
		return new Tulinkry\UploadResponse ( $this -> url . "/" . $name, $this -> thumbnail_url . '/thumbnail_80x80_' . $name, $photo );
	}

	public function saveImage ( $path_to_data, $data, $flush = self::FLUSH )
	{
		$arrContextOptions=array(
		    "ssl"=>array(
		        "verify_peer"=>false,
		        "verify_peer_name"=>false,
		    ),
		); 
		$image = file_get_contents ( $path_to_data, false, stream_context_create($arrContextOptions) );
		$img = Image::fromString ( $image );

		do {
			$name = Strings::random(10) . '.' . "fb_image.jpg";
		} while (file_exists($path = $this->path . "/" . $name));

		$photo_data = array_merge ( $data, [ "url" =>  $this -> url ."/" . $name, "path" => $path ] );
		$img -> save ( $path );

		$this -> fixImageRotation ( $path );

		$photo = $this -> photos -> create ( $photo_data );
        for ( $i = 1; $i <= 1; $i ++ )
        {
        	$img -> resize ( "300px", null )->save( $this -> thumbnail_path . '/thumbnail_' . ($i*80) . "x" . ($i*80) . "_" . $name);
        	$thb = array_merge ( $data, [ "url" => $this -> thumbnail_url . '/thumbnail_' . ($i*80) . "x" . ($i*80) . "_" . $name,
        								  "path" => $this -> thumbnail_path . '/thumbnail_' . ($i*80) . "x" . ($i*80) . "_" . $name
        								] );
        	unset ( $thb [ "gallery" ] );
        	$thumbnail = $this -> photos -> create ( $thb );
        	$photo -> addThumbnail ( $thumbnail );
        }

        try{
			$this -> photos -> insert ( $photo, $flush );
		}
		catch ( \Exception $e )
		{
			$this -> cleanup ( $photo );
			throw $e;
		}
		return $photo;
	}

	public function save($content, $filename)
	{
		do {
			$name = Strings::random(10) . '.' . $filename;
		} while (file_exists($path = $this->path . "/" . $name));

		file_put_contents($path, $content);
		return new Image($path);
	}

	protected function cleanup ( $photo )
	{
		if( @file_exists($photo -> path) ) {
            @unlink($photo -> path);
        }		

        
        foreach ( $photo -> thumbnails as $thumbnail )
        {
			if( @file_exists($thumbnail -> path) ) 
			{
	            @unlink(  $thumbnail -> path);
	        }
	        $photo -> removeThumbnail ( $thumbnail );			
        }		
	}

}