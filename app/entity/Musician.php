<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tulinkry\Model\Doctrine\Entity\BaseEntity;


/**
 * @ORM\Entity
 * @ORM\Table ( name = "musicians" )
 */
class Musician extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column ( name="musician_id", type="integer" )
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
	 * @ORM\Column ( type = "string", length = 255 )
	 */ 
	protected $surname = "";

    /**
     * @ORM\ManyToOne ( targetEntity = "Photo", cascade = { "all" } )
     * @ORM\JoinColumn ( name = "photo_id", referencedColumnName = "photo_id" )
     */
    protected $photo = NULL;

	public function __construct ()
	{
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
     * @return Musician
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
     * @return Musician
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
     * Set surname
     *
     * @param string $surname
     * @return Musician
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set photo
     *
     * @param \Entity\Photo $photo
     * @return Musician
     */
    public function setPhoto(\Entity\Photo $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Entity\Photo 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    
    public function getDescription ()
    {
        $d = $this -> getId ();
        if ( parent::useId )
            $d .= " [" . $this -> getId () . "]";
        return $d;
    }
};