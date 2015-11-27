<?php


namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tulinkry\Model\Doctrine\Entity\BaseEntity;
use Tulinkry;

use Nette;

/**
 * @ORM\Entity
 * @ORM\Table ( name = "posts" )
 */
class Post extends BaseEntity
{
    const TYPE_VIDEO = "video";
    const TYPE_PHOTO = "photo";
    const TYPE_STATUS = "status";
    const TYPE_LINK = "link";


    static public $types = array ( 
                            self::TYPE_LINK => "Odkaz", 
                            self::TYPE_STATUS => "Text", 
                            self::TYPE_PHOTO => "Fotka", 
                            self::TYPE_VIDEO => "Video" );

	/**
	 * @ORM\Id
	 * @ORM\Column ( name="post_id", type="integer" )
	 * @ORM\GeneratedValue
	 */
	protected $id;
    /**
     * @ORM\Column ( type = "string", length = 255, nullable = true )
     */
    protected $name;
    /**
     * @ORM\Column ( type = "string", length = 255 )
     */
    protected $type;

    /**
     * @ORM\Column ( type = "string", length = 255, nullable = true )
     */
    protected $link;

	/**
	 * @ORM\Column ( type = "blob", nullable = true )
	 */ 
	protected $message = "";
	/**
	 * @ORM\Column ( type = "datetime" )
	 */ 
	protected $datum = NULL;
    /**
     * @ORM\ManyToOne ( targetEntity = "Photo" )
     * @ORM\JoinColumn ( name = "photo_id", referencedColumnName = "photo_id" )
     */
    protected $image = NULL;
	/**
	 * @ORM\Column ( type = "boolean" )
	 */ 
	protected $hidden = false;
	/**
	 * @ORM\Column ( type = "string", length = 255, nullable = true, unique = true )
	 */ 
	protected $fb = "";

	public function __construct ()
	{
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
     * Set message
     *
     * @param string $message
     * @return Post
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        if ( ! $this->message )
            return "";
        if ( is_string ( $this->message ) )
            return $this->message;
        return $this->message = stream_get_contents ( $this->message );
    }

    /**
     * Set datum
     *
     * @param \DateTime $datum
     * @return Post
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
     * @return Post
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
     * Set name
     *
     * @param boolean $name
     * @return Post
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return boolean 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set link
     *
     * @param boolean $link
     * @return Post
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return boolean 
     */
    public function getLink()
    {
        return $this->link;
    }


    /**
     * Set type
     *
     * @param boolean $type
     * @return Post
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set fb
     *
     * @param string $fb
     * @return Post
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
     * Set image
     *
     * @param \Entity\Photo $image
     * @return Post
     */
    public function setImage(\Entity\Photo $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Entity\Photo 
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getDescription ()
    {
        $d = $this -> getMessage ();
        if ( parent::useId )
            $d .= " [" . $this -> getId () . "]";
        return $d;
    }
};