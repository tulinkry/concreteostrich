<?php

namespace Model;

use Tulinkry\Model\Doctrine\BaseModel;
use Tulinkry\Gallery\Image;
use Tulinkry\Utils\Strings;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Tulinkry;
use Doctrine\Common\Collections\ArrayCollection;

class EventModel extends BaseModel
{

	const INSERT = 1;
	const NO_INSERT = 0;


	protected $galleryStorage;

	public function __construct ( EntityManager $em, GalleryStorage $g )
	{
		parent::__construct ( $em );
		$this -> galleryStorage = $g;
	}

    public function remove ( $entity, $transactional = self::FLUSH )
    {
        if ( ! $entity )
            throw new \Exception ( "No entity given." );
        $image = $entity -> cover;
        $ret = parent::remove ( $entity );
        if ( $image )
            $this -> galleryStorage -> delete ( $image -> id );
        return $ret;
    }


	public function past ( $n = 5 )
	{
		$query = $this -> em -> createQuery( "SELECT e FROM Entity\Event e
											  WHERE e . start < ?1 AND
											  		e . hidden = 0
											  ORDER BY e . start DESC, e . end DESC" );

		$query -> setParameter ( 1, date ( "Y-m-d H:i:s" ) );
		$query -> setFirstResult ( 0 );
		$query -> setMaxResults ( $n );


		return $query -> getResult ();
	}


	public function next ( $n = 5 )
	{
		$query = $this -> em -> createQuery( "SELECT e FROM Entity\Event e
											  WHERE e . start >= ?1 AND
											  		e . hidden = 0
											  ORDER BY e . start DESC, e . end DESC" );

		$query -> setParameter ( 1, date ( "Y-m-d H:i:s" ) );

		$result = new ArrayCollection ( $query -> getResult () );

		if ( $result -> count () <= $n )
			return $result;

		return $result -> slice ( $result -> count () - $n, $n );
	}




	public function import ( $event, $insert = self::NO_INSERT )
	{

		//print_r ( $event );

        if ( count ( $this -> by ( [ "fb" => $event [ "id"  ] ] ) ) > 0 )
            throw new \Exception("Error Processing Request", 1);

		if ( array_key_exists ( "cover", $event ) &&
			 array_key_exists ( "source", $event [ "cover" ] ) )
		{
			// we have cover photo
			/*
			    [cover] => Nette\Utils\ArrayHash Object
		        (
		            [cover_id] => 10152052697841856
		            [source] => https://scontent-a.xx.fbcdn.net/hphotos-xap1/t31.0-8/s720x720/10317706_10152052697841856_1423486888452947868_o.jpg
		            [offset_y] => 24
		            [offset_x] => 0
		        )
		     */
			
			$g = $this -> galleryStorage -> getGalleryBy ( [ "name" => "Facebook" ] );
			if ( count ( $g ) )
				$g = $g [ 0 ];
			else
				throw new Tulinkry\Exception ( "No gallery 'Facebook'" );

			$event [ "cover" ] = $this -> galleryStorage -> saveImage (  $event [ "cover" ] [ "source" ], [ "gallery" => $g ], self::NO_FLUSH );

		}

		if ( array_key_exists ( "start_time", $event ) )
			$event [ "start" ] = new Tulinkry\DateTime ( $event [ "start_time" ] );
		if ( array_key_exists ( "end_time", $event ) )
			$event [ "end" ] = new Tulinkry\DateTime ( $event [ "end_time" ] );
		else
			$event [ "end" ] = new Tulinkry\DateTime ( $event [ "start_time" ] );

		if ( array_key_exists ( "venue", $event ) )
		{
			if ( array_key_exists ( "latitude", $event [ "venue" ] ) )
				$event [ "latitude" ] = $event [ "venue" ] ["latitude"];
			if ( array_key_exists ( "longitude", $event [ "venue" ] ) ) 
				$event [ "longitude" ] = $event [ "venue" ] [ "longitude" ];
		}
		if ( array_key_exists ( "description", $event ) )
			$event [ "content" ] = $event [ "description" ];

		$event [ "fb" ] = $event [ "id" ];
		unset ( $event [ "id" ] );

		$event [ "hidden" ] = 1;

		$e = $this -> create ( $event );

		if ( $insert === self::INSERT )
			$this -> insert ( $e, self::NO_FLUSH );


		return $e;
	}
}

/*
Nette\Utils\ArrayHash Object
(
    [cover] => Nette\Utils\ArrayHash Object
        (
            [cover_id] => 10152052697841856
            [source] => https://scontent-a.xx.fbcdn.net/hphotos-xap1/t31.0-8/s720x720/10317706_10152052697841856_1423486888452947868_o.jpg
            [offset_y] => 24
            [offset_x] => 0
        )

    [location] => Rock Café
    [end_time] => 2014-09-30T23:00:00+0200
    [description] => Uzavíráme první stovku koncertů, která vyvrcholí slavnostním 100. gigem v Rock Café, kde kromě osvědčených a léty ošlehaných písní zazní i nejnovější, nastupující hity.

Speciálním hostem večera bude Vít Sázavský, který stál na počátku 80. let u zrodu legendární kapely Nerez a dodnes je jedním z frontmanů pokračujících Neřež.

Warm-up obstará funk-jazzová kapela Concrete Ostrich, která si svým obsazením nezadá s malým orchestrem. Založil ji v roce 2012 Leo Lukáš se záměrem vyhovět dvěma skupinám posluchačů. Lidem, kteří si na koncertech rádi zatrsají před pódiem i těm, co stojí dále a spíše poslouchají. Repertoár v podání nastupující generace muzikantů tvoří kromě autorských kompozic i převzaté skladby funkových, jazzových i popových kapel a interpretů.

SPECIAL GUEST: Vít Sázavský (Neřež)
WARMUP: Concrete Ostrich

Running Sushi, za pětku v opeře utoneš v nádheře.
    [is_date_only] => 
    [name] => Kilo Running Sushi - slavnostní 100. koncert
    [owner] => Nette\Utils\ArrayHash Object
        (
            [category] => Musician/band
            [name] => Running Sushi
            [id] => 44664501855
        )

    [id] => 741854359212378
    [privacy] => OPEN
    [start_time] => 2014-09-30T20:30:00+0200
    [timezone] => Europe/Prague
    [updated_time] => 2014-08-29T14:12:08+0000
    [venue] => Nette\Utils\ArrayHash Object
        (
            [latitude] => 50.082039942098
            [longitude] => 14.418673998731
            [city] => Prague
            [state] => 
            [country] => Czech Republic
            [id] => 208456445831747
            [street] => Národní 20
            [zip] => 110 00
        )

)

*/