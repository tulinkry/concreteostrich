<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tulinkry\Model\Doctrine\Entity\BaseEntity;
use Nette;
use Tulinkry;

/**
 * @ORM\Entity
 * @ORM\Table ( name = "parameters" )
 */
class Parameters extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column ( name="parameters_id", type="integer" )
	 * @ORM\GeneratedValue
	 */
	protected $id;
  
	/**
	 * @ORM\Column ( type = "datetime" )
	 */ 
	protected $events_last_update = NULL;

	/**
	 * @ORM\Column ( type = "datetime" )
	 */ 
	protected $posts_last_update = NULL;


    public function __construct ()
    {
        //$this -> last_update = new Tulinkry\DateTime;
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
     * Set events_last_update
     *
     * @param string $events_last_update
     * @return Photo
     */
    public function setEvents_last_update($events_last_update)
    {
        $this->events_last_update = $events_last_update;

        return $this;
    }

    /**
     * Get events_last_update
     *
     * @return string 
     */
    public function getEvents_last_update()
    {
        return $this->events_last_update;
    }


    /**
     * Set posts_last_update
     *
     * @param string $posts_last_update
     * @return Photo
     */
    public function setPosts_last_update($posts_last_update)
    {
        $this->posts_last_update = $posts_last_update;

        return $this;
    }

    /**
     * Get posts_last_update
     *
     * @return string 
     */
    public function getPosts_last_update()
    {
        return $this->posts_last_update;
    }

}