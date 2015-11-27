<?php

namespace Model;

use Tulinkry\Model\Doctrine\BaseModel;

use Kdyby\Doctrine\EntityManager;
use Entity\Post;
use Nette;
use Tulinkry;

class PostModel extends BaseModel
{

    const YOUTUBE_VIDEO_URL = "https://www.youtube.com/embed/%s";
    const FACEBOOK_VIDEO_URL = "https://www.facebook.com/video/embed?video_id=%s";
    const INSERT = 1;
    const NO_INSERT = 0;

    static public $videoProcessors = array ( self::YOUTUBE_VIDEO_URL => "getYoutubeId",
                                             self::FACEBOOK_VIDEO_URL => "getFacebookId" );

    protected $galleryStorage;

    public function __construct ( EntityManager $em, GalleryStorage $g )
    {
        parent::__construct ( $em );
        $this -> galleryStorage = $g;

    }

    public function remove ( $entity, $transactional = self::FLUSH )
    {
        if ( ! $entity )
            throw new Tulinkry\Exception ( "No entity given." );
        $image = $entity -> image;
        $ret = parent::remove ( $entity );
        if ( $image )
            $this -> galleryStorage -> delete ( $image -> id );
        return $ret;
    }

    static public function getFacebookId ( $string )
    {
        /*
            https://www.facebook.com/video.php?v=822878377735312
            https://www.facebook.com/video.php?video_id=822878377735312
            https://www.facebook.com/ConcreteOstrich/videos/958838667472615/
        */
        if ( preg_match ( "/\/\/(www\.)?facebook\.com\/video\.php\?v/", $string ) === 1 )
        {
            if ( ( $id = preg_replace ( "/.*(video_id=|v=)([a-zA-Z0-9_-]+).*/", "$2", $string ) ) === $string )
                throw new Tulinkry\Exception("Error parsing facebook url");
        }
        elseif ( preg_match ( "/\/\/(www\.)?facebook\.com\/[a-zA-Z0-9_-]+\/videos\/[0-9]+/", $string ) )
        {
            if ( ( $id = preg_replace ( "/.*\/videos\/([a-zA-Z0-9_-]+).*/", "$1", $string ) ) === $string )
                throw new Tulinkry\Exception("Error parsing facebook url");
        }
        else
            throw new Tulinkry\Exception("Error parsing facebook url");
        return $id;
    }

    static public function getYoutubeId ( $string )
    {
        /*
            https://www.youtube.com/watch?v=xaVoNvo9Fnc&feature=youtu.be
            http://youtu.be/xaVoNvo9Fnc
            http://www.youtube.com/v/xaVoNvo9Fnc?version=3&autohide=1&autoplay=1
            https://www.youtube.com/watch?v=FHockgflgvg&list=UUz5E_NpN0t5FOGQAuDLg3UQ
            http://www.youtube.com/v/FHockgflgvg?list=UUz5E_NpN0t5FOGQAuDLg3UQ&version=3&autoplay=1
        */
        if ( preg_match ( "/\/\/(www\.)?youtube/", $string ) === 1 )
        {
            if ( ( $id = preg_replace ( "/.*(v=|v\/)([a-zA-Z0-9_-]+).*/", "$2", $string ) ) === $string )
                throw new Tulinkry\Exception("Error parsing youtube url");
        }
        elseif ( preg_match ( "/\/\/(www\.)?youtu\.be/", $string ) === 1 )
        {
            if ( ( $id = preg_replace ( "/.*\/([a-zA-Z0-9_-]+).*/", "$1", $string ) ) === $string )
                throw new Tulinkry\Exception("Error parsing youtube url");
        }
        else
            throw new Tulinkry\Exception("Error parsing youtube url");
            

        return $id;
    }

    public function import ( $post, $insert = self::NO_INSERT  )
    {
        //print_r ( $post );
        if ( count ( $this -> by ( [ "fb" => $post [ "id"  ] ] ) ) > 0 )
            throw new Tulinkry\Exception("Item already existed.");

        if ( ! ( array_key_exists ( "type", $post ) && in_array ( $post [ "type" ], array_keys ( Post::$types ) ) ) )
            throw new Tulinkry\Exception("Type is not allowed.");

        if ( ! ( array_key_exists ( "message", $post ) || array_key_exists ( "name", $post ) ) ||
             ! ( ( array_key_exists ( "message", $post ) && strlen ( $post [ "message" ] ) ) ||
               ( array_key_exists ( "name", $post ) && strlen ( $post [ "name" ] ) ) ) )
            throw new Tulinkry\Exception("Post does not have name neither message content.");

        if ( ! array_key_exists ( "name", $post ) || ! strlen ( $post [ "name" ] ) )
            $post [ "name" ] = ucfirst ( $post [ "type" ] );

            
        if ( array_key_exists ( "created_time", $post ) )
            $post [ "datum" ] = new Tulinkry\DateTime ( $post [ "created_time" ] );

        if ( array_key_exists ( "picture", $post ) )  
        {
            /*
             * create photo and save
             */

            $g = $this -> galleryStorage -> getGalleryBy ( [ "name" => "Facebook" ] );
            if ( count ( $g ) )
                $g = $g [ 0 ];
            else
                throw new Tulinkry\Exception("No or no unique gallery 'Facebook'");

            $post [ "image" ] = $this -> galleryStorage -> saveImage (  $post [ "picture" ], [ "gallery" => $g ], self::NO_FLUSH );
        }

        if ( array_key_exists ( "link", $post ) && 
             array_key_exists ( "type", $post ) && 
             ( $post [ "type" ] == Post::TYPE_VIDEO ||
               $post [ "type" ] == Post::TYPE_LINK ) ) 
        {
            foreach ( self::$videoProcessors as $url => $func )
            {
                try{
                    $post [ "link" ] = sprintf ( $url, self::$func ( $post [ "link" ] ) );
                    $post [ "type" ] = Post::TYPE_VIDEO;
                } catch ( \Exception $e )
                {}
            }
        }

        if ( array_key_exists ( "message", $post ) )
        {
            $post [ "message" ] = preg_replace ( '%(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?%iu', "", $post [ "message" ] );
            // ... others ... //
        }


        $post [ "fb" ] = $post [ "id" ];
        unset ( $post [ "id" ] );   
        
        $post [ "hidden" ] = 1;

        $e = $this -> create ( $post );

            
        if ( $insert === self::INSERT )
            $this -> insert ( $e, self::NO_FLUSH ); 


        return $e;

    }

}

/*
set foreign_key_checks = 0;# MySQL vrátil prázdný výsledek (tj. nulový počet řádků).

TRUNCATE `events`;# MySQL vrátil prázdný výsledek (tj. nulový počet řádků).

TRUNCATE `photos`;# MySQL vrátil prázdný výsledek (tj. nulový počet řádků).

TRUNCATE `posts`;# MySQL vrátil prázdný výsledek (tj. nulový počet řádků).

set foreign_key_checks = 1;# MySQL vrátil prázdný výsledek (tj. nulový počet řádků).
*/

/*


Nette\Utils\ArrayHash Object
(
    [id] => 503783986311421_817748091581674
    [from] => Nette\Utils\ArrayHash Object
        (
            [category] => Musician/band
            [name] => Concrete Ostrich
            [id] => 503783986311421
        )

    [story] => Concrete Ostrich updated their cover photo.
    [story_tags] => Nette\Utils\ArrayHash Object
        (
            [0] => Nette\Utils\ArrayHash Object
                (
                    [0] => Nette\Utils\ArrayHash Object
                        (
                            [id] => 503783986311421
                            [name] => Concrete Ostrich
                            [offset] => 0
                            [length] => 16
                            [type] => page
                        )

                )

        )

    [picture] => https://fbcdn-sphotos-h-a.akamaihd.net/hphotos-ak-xpf1/v/t1.0-9/s130x130/1533745_817747928248357_6874669432014287535_n.jpg?oh=819b9e84647804165362cadd2eb573ab&oe=54D52EB2&__gda__=1427142709_420d4ace09d53187593875263bad7aec
    [link] => https://www.facebook.com/ConcreteOstrich/photos/a.503792222977264.108555.503783986311421/817747928248357/?type=1&relevant_count=1
    [icon] => https://fbstatic-a.akamaihd.net/rsrc.php/v2/yz/r/StEh3RhPvjk.gif
    [privacy] => Nette\Utils\ArrayHash Object
        (
            [value] => 
        )

    [type] => photo
    [object_id] => 817747928248357
    [created_time] => 2014-11-18T19:41:52+0000
    [updated_time] => 2014-11-18T19:41:52+0000
    [likes] => Nette\Utils\ArrayHash Object
        (
            [data] => Nette\Utils\ArrayHash Object
                (
                    [0] => Nette\Utils\ArrayHash Object
                        (
                            [id] => 742948315772790
                            [name] => Jana Šabinská
                        )

                    [1] => Nette\Utils\ArrayHash Object
                        (
                            [id] => 10203343766897154
                            [name] => Andrea Šímová
                        )

                    [2] => Nette\Utils\ArrayHash Object
                        (
                            [id] => 1583591911870164
                            [name] => Natalie Preclíkova
                        )

                    [3] => Nette\Utils\ArrayHash Object
                        (
                            [id] => 748048441930862
                            [name] => Bar Bi
                        )

                    [4] => Nette\Utils\ArrayHash Object
                        (
                            [id] => 10203518870107487
                            [name] => Marťa Medků
                        )

                )

            [paging] => Nette\Utils\ArrayHash Object
                (
                    [cursors] => Nette\Utils\ArrayHash Object
                        (
                            [after] => MTAyMDM1MTg4NzAxMDc0ODc=
                            [before] => NzQyOTQ4MzE1NzcyNzkw
                        )

                )

        )

)

*/

