<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tulinkry\Model\Doctrine\Entity\BaseEntity;
use Nette;
use Tulinkry;

/**
 * @ORM\Entity
 * @ORM\Table ( name = "galleries" )
 */
class Gallery extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column ( name="gallery_id", type="integer" )
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
    protected $datum = NULL;
    /**
     * @ORM\Column ( type = "boolean" )
     */ 
    protected $hidden = false;
    /**
     * @ORM\OneToMany ( targetEntity = "Photo", mappedBy = "gallery", cascade={ "persist", "remove" } )
     * @ORM\JoinColumn ( name = "gallery_id", referencedColumnName = "gallery_id" )
     */
    protected $photos;

	public function __construct ()
	{
		$this -> photos = new ArrayCollection;
        $this -> datum = new Tulinkry\DateTime;
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
     * @return Gallery
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
     * @return Gallery
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
     * Set datum
     *
     * @param \DateTime $datum
     * @return Gallery
     */
    public function setDatum($datum)
    {
        $this->datum = $datum;

        return $this;
    }

    /**
     * Get datum
     *
     * @return \DateTime 
     */
    public function getDatum()
    {
        return $this -> datum;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     * @return Gallery
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
     * Add photos
     *
     * @param \Entity\Photo $photos
     * @return Gallery
     */
    public function addPhoto(\Entity\Photo $photos)
    {
        $this->photos[] = $photos;
        $photos->setGallery($this);

        return $this;
    }

    /**
     * Remove photos
     *
     * @param \Entity\Photo $photos
     */
    public function removePhoto(\Entity\Photo $photos)
    {
        $this->photos->removeElement($photos);
        $photos -> setGallery ( NULL );
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    public function getDescription ()
    {
        $d = $this -> getName ();
        if ( parent::useId )
            $d .= " [" . $this -> getId () . "]";
        return $d;
    }
};