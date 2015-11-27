<?php

namespace Model;

use Tulinkry\Model\Doctrine\BaseModel;
use Kdyby\Doctrine\ResultSet;
use Doctrine\ORM\Tools\Pagination\Paginator;

class GalleryModel extends BaseModel
{

	public function loadPhotos ( $limit = 50, $offset = 0 )
	{

		$query = $this -> em -> createQuery ( "SELECT p FROM Entity\Photo p
											   JOIN p . thumbnails t
											   JOIN p . gallery g
											   WHERE g . hidden = 0 AND p . hidden = 0
											   ORDER BY g . datum DESC, g . name ASC, p . rank DESC, p . datum DESC" );

		$query -> setFirstResult ( $offset );
		$query -> setMaxResults ( $limit );

		$res = [];

		if ( count ( $result = $query -> getResult () ) )
		{
			$prevGalleryId = 0;
			foreach ( $result as $photo )
			{
				if ( $prevGalleryId != $photo -> gallery -> id )
					$res [] = $photo -> gallery;
				$res [] = $photo;
				$prevGalleryId = $photo -> gallery -> id;				
			}
		}

		return $res;

		$query = $this -> em -> createQuery( "SELECT g, p, t FROM Entity\Gallery g
										      JOIN g . photos p
											  JOIN p . thumbnails t
											  ORDER BY g . datum DESC, g . name ASC, p . rank DESC, p . datum DESC" );

		$res = $query -> getResult ();

		$r = [];
		foreach ( $res as $entity )
		{
			$r [] = $entity;
			foreach ( $entity -> photos as $p )
				$r [] = $p;
		}

		$second = [];
		$i = 0;
		foreach ( $r as $e )
		{
			if ( $i >= $offset && $i < $limit + $offset )
				$second [] = $e;
			$i ++;
		}
		return $second;

	}
}

