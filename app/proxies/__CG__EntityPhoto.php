<?php

namespace Kdyby\GeneratedProxy\__CG__\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Photo extends \Entity\Photo implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }

    /**
     * {@inheritDoc}
     * @param string $name
     */
    public function & __get($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', array($name));

        return parent::__get($name);
    }

    /**
     * {@inheritDoc}
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__set', array($name, $value));

        return parent::__set($name, $value);
    }

    /**
     * {@inheritDoc}
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__isset', array($name));

        return parent::__isset($name);

    }

    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'id', 'content', 'path', 'url', 'rank', 'datum', 'updated', 'hidden', '' . "\0" . 'Entity\\Photo' . "\0" . 'thumbnails', '' . "\0" . 'Entity\\Photo' . "\0" . 'parent', 'fb', 'gallery');
        }

        return array('__isInitialized__', 'id', 'content', 'path', 'url', 'rank', 'datum', 'updated', 'hidden', '' . "\0" . 'Entity\\Photo' . "\0" . 'thumbnails', '' . "\0" . 'Entity\\Photo' . "\0" . 'parent', 'fb', 'gallery');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Photo $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($content)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContent', array($content));

        return parent::setContent($content);
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContent', array());

        return parent::getContent();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdated($updated)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdated', array($updated));

        return parent::setUpdated($updated);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdated()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdated', array());

        return parent::getUpdated();
    }

    /**
     * {@inheritDoc}
     */
    public function setPath($path)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPath', array($path));

        return parent::setPath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPath', array());

        return parent::getPath();
    }

    /**
     * {@inheritDoc}
     */
    public function setUrl($url)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUrl', array($url));

        return parent::setUrl($url);
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUrl', array());

        return parent::getUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setRank($rank)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRank', array($rank));

        return parent::setRank($rank);
    }

    /**
     * {@inheritDoc}
     */
    public function getRank()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRank', array());

        return parent::getRank();
    }

    /**
     * {@inheritDoc}
     */
    public function setDatum($datum)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDatum', array($datum));

        return parent::setDatum($datum);
    }

    /**
     * {@inheritDoc}
     */
    public function getDatum()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDatum', array());

        return parent::getDatum();
    }

    /**
     * {@inheritDoc}
     */
    public function setHidden($hidden)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHidden', array($hidden));

        return parent::setHidden($hidden);
    }

    /**
     * {@inheritDoc}
     */
    public function getHidden()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHidden', array());

        return parent::getHidden();
    }

    /**
     * {@inheritDoc}
     */
    public function setFb($fb)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFb', array($fb));

        return parent::setFb($fb);
    }

    /**
     * {@inheritDoc}
     */
    public function getFb()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFb', array());

        return parent::getFb();
    }

    /**
     * {@inheritDoc}
     */
    public function addThumbnail(\Entity\Photo $thumbnails)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addThumbnail', array($thumbnails));

        return parent::addThumbnail($thumbnails);
    }

    /**
     * {@inheritDoc}
     */
    public function removeThumbnail(\Entity\Photo $thumbnails)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeThumbnail', array($thumbnails));

        return parent::removeThumbnail($thumbnails);
    }

    /**
     * {@inheritDoc}
     */
    public function getThumbnails()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getThumbnails', array());

        return parent::getThumbnails();
    }

    /**
     * {@inheritDoc}
     */
    public function getThumbnail($offset = 0)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getThumbnail', array($offset));

        return parent::getThumbnail($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function setParent(\Entity\Photo $parent = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setParent', array($parent));

        return parent::setParent($parent);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getParent', array());

        return parent::getParent();
    }

    /**
     * {@inheritDoc}
     */
    public function setGallery(\Entity\Gallery $gallery = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGallery', array($gallery));

        return parent::setGallery($gallery);
    }

    /**
     * {@inheritDoc}
     */
    public function getGallery()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGallery', array());

        return parent::getGallery();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', array());

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'toArray', array());

        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function array_map(callable $val, callable $key, array $array)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'array_map', array($val, $key, $array));

        return parent::array_map($val, $key, $array);
    }

    /**
     * {@inheritDoc}
     */
    public function toSelect($collection)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'toSelect', array($collection));

        return parent::toSelect($collection);
    }

    /**
     * {@inheritDoc}
     */
    public function __call($name, $args)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__call', array($name, $args));

        return parent::__call($name, $args);
    }

    /**
     * {@inheritDoc}
     */
    public function __unset($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__unset', array($name));

        return parent::__unset($name);
    }

}