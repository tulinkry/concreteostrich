<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tulinkry\Model\Doctrine\Entity\BaseEntity;
use Nette;
use Tulinkry;

/**
 * @ORM\Entity
 * @ORM\Table ( name = "events" )
 */
class Event extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column ( name="event_id", type="integer" )
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column ( type = "blob" )
     */ 
    protected $content = "";
    /**
     * @ORM\Column ( type = "string", length = 255 )
     */ 
    protected $name = "";
    /**
     * @ORM\Column ( type = "datetime" )
     */ 
    protected $start = NULL;
    /**
     * @ORM\Column ( type = "datetime" )
     */ 
    protected $end = NULL;
    /**
     * @ORM\Column ( type = "string", length = 255 )
     */ 
    protected $location = "";
    /**
     * @ORM\Column ( type = "float" )
     */ 
    protected $latitude = 0.0;
    /**
     * @ORM\Column ( type = "float" )
     */ 
    protected $longitude = 0.0;
    /**
     * @ORM\ManyToOne ( targetEntity = "Photo" )
     * @ORM\JoinColumn ( name = "photo_id", referencedColumnName = "photo_id" )
     */
    protected $cover = NULL;
    /**
     * @ORM\Column ( type = "boolean" )
     */ 
    protected $hidden = false;
    /**
     * @ORM\Column ( type = "string", length = 255 )
     */ 


    /**
     * @ORM\Column ( type = "string", length = 255, nullable = true, unique = true )
     */ 
    protected $fb = "";


    public function __construct ()
    {
        $this -> start = new Tulinkry\DateTime;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Event
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        if ( ! $this->content )
            return "";
        if ( is_string ( $this->content ) )
            return $this->content;
        return $this->content = stream_get_contents ( $this->content );
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return Event
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this -> start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Event
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this -> end;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Event
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return Event
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return Event
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     * @return Event
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get hidden
     *
     * @return boolean 
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set fb
     *
     * @param string $fb
     * @return Event
     */
    public function setFb($fb)
    {
        $this->fb = $fb;

        return $this;
    }

    /**
     * Get fb
     *
     * @return string 
     */
    public function getFb()
    {
        return $this->fb;
    }

    /**
     * Set cover
     *
     * @param \Entity\Photo $cover
     * @return Event
     */
    public function setCover(\Entity\Photo $cover = null)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return \Entity\Photo 
     */
    public function getCover()
    {
        return $this->cover;
    }

    public function getDescription ()
    {
        $d = $this -> getContent ();
        if ( parent::useId )
            $d .= " [" . $this -> getId () . "]";
        return $d;
    }
};