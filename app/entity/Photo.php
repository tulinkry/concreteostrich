<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tulinkry\Model\Doctrine\Entity\BaseEntity;
use Nette;
use Tulinkry;

/**
 * @ORM\Entity
 * @ORM\Table ( name = "photos" )
 */
class Photo extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column ( name="photo_id", type="integer" )
	 * @ORM\GeneratedValue
	 */
	protected $id;
	/**
	 * @ORM\Column ( type = "string", length = 255 )
	 */ 
	protected $content = "";
    /**
     * @ORM\Column ( type = "string", length = 255 )
     */ 
    protected $path = "";
    /**
     * @ORM\Column ( type = "string", length = 255 )
     */ 
    protected $url = "";
	/**
	 * @ORM\Column ( type = "decimal", length = 255 )
	 */ 
	protected $rank = 0.0;
    /**
     * @ORM\Column ( type = "datetime" )
     */ 
    protected $datum = NULL;
    /**
     * @ORM\Column ( type = "datetime" )
     */ 
    protected $updated = NULL;
	/**
	 * @ORM\Column ( type = "boolean" )
	 */ 
	protected $hidden = false;
    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="parent", fetch = "EAGER", cascade = {"all"})
     **/
    private $thumbnails;
    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="thumbnails")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="photo_id")
     **/
    private $parent;
	/**
	 * @ORM\Column ( type = "string", length = 255 )
	 */ 
	protected $fb = "";
    /**
     * @ORM\ManyToOne ( targetEntity = "Gallery", inversedBy = "photos" )
     * @ORM\JoinColumn ( name = "gallery_id", referencedColumnName = "gallery_id" )
     */
    protected $gallery;	

	public function __construct ()
	{
        $this -> datum = new Tulinkry\DateTime;
        $this -> updated = new Tulinkry\DateTime;
        $this -> thumbnails = new ArrayCollection();
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
     * @return Photo
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
        return $this->content;
    }


    /**
     * Set updated
     *
     * @param string $updated
     * @return Photo
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return string 
     */
    public function getUpdated()
    {
        return $this -> updated;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Photo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Photo
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set rank
     *
     * @param string $rank
     * @return Photo
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set datum
     *
     * @param \DateTime $datum
     * @return Photo
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
     * @return Photo
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
     * @return Photo
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
     * Add thumbnails
     *
     * @param \Entity\Photo $thumbnails
     * @return Photo
     */
    public function addThumbnail(\Entity\Photo $thumbnails)
    {
        $this->thumbnails[] = $thumbnails;
        $thumbnails -> setParent ( $this );
        return $this;
    }

    /**
     * Remove thumbnails
     *
     * @param \Entity\Photo $thumbnails
     */
    public function removeThumbnail(\Entity\Photo $thumbnails)
    {
        $this->thumbnails->removeElement($thumbnails);
        $thumbnails -> setParent ( NULL );
    }

    /**
     * Get thumbnails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    /**
     * Get thumbnail
     *
     * @return \Entity\Photo 
     */
    public function getThumbnail( $offset = 0 )
    {
        if ( $this -> thumbnails -> offsetExists ( $offset ) )
            return $this -> thumbnails -> offsetGet ( $offset );
        return NULL;
    }

    /**
     * Set parent
     *
     * @param \Entity\Photo $parent
     * @return Photo
     */
    public function setParent(\Entity\Photo $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Entity\Photo 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set gallery
     *
     * @param \Entity\Gallery $gallery
     * @return Photo
     */
    public function setGallery(\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }
    public function getDescription ()
    {
        $d = $this -> getId ();
        if ( parent::useId )
            $d .= " [" . $this -> getId () . "]";
        return $d;
    }
};