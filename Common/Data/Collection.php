<?php

/**
 * This File is part of the Selene\Module\Common\Data package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Data;

use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\Common\Traits\Setter;
use \Selene\Module\Common\Helper\ListHelper;
use \Selene\Module\Common\Interfaces\ArrayableInterface;

/**
 * @class Collection implements CollectionInterface, \ArrayAccess, \IteratorAggregate
 * @see CollectionInterface
 * @see \ArrayAccess
 * @see \IteratorAggregate
 *
 * @package Selene\Module\Common\Data
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Collection implements CollectionInterface, \ArrayAccess, \IteratorAggregate, ArrayableInterface
{
    use Getter, Setter;

    /**
     * attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * @access public
     * @return mixed
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * initialize
     *
     * @param array $data
     *
     * @access public
     * @return mixed
     */
    public function initialize(array $data)
    {
        $this->attributes = $data;
    }

    /**
     * set
     *
     * @param mixed $attribute
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function set($attribute, $value)
    {
        return $this->attributes[$attribute] = $value;
    }

    /**
     * get
     *
     * @param mixed $attribute
     * @param mixed $default
     *
     * @access public
     * @return mixed
     */
    public function get($attribute, $default = null)
    {
        return $this->getDefault($this->attributes, $attribute, $default);
    }

    /**
     * all
     *
     * @access public
     * @return mixed
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * has
     *
     * @param mixed $attribute
     *
     * @access public
     * @return mixed
     */
    public function has($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * delete
     *
     * @param mixed $attribute
     *
     * @access public
     * @return mixed
     */
    public function delete($attribute = null)
    {
        if (null !== $attribute) {
            unset($this->attributes[$attribute]);
        } else {
            $this->attributes = [];
        }
    }

    /**
     * merge
     *
     * @param CollectionInterface $collection
     *
     * @access public
     * @return mixed
     */
    public function merge(CollectionInterface $collection)
    {
        $this->attributes = array_merge($this->all(), $collection->all());
    }

    /**
     * getIterator
     *
     * @access public
     * @return mixed
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }

    /**
     * offsetSet
     *
     * @param mixed $attr
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function offsetSet($attr, $value)
    {
        return $this->set($attr, $value);
    }

    /**
     * offsetGet
     *
     * @param mixed $attr
     *
     * @access public
     * @return mixed
     */
    public function offsetGet($attr)
    {
        return $this->get($attr);
    }

    /**
     * offsetExists
     *
     * @param mixed $attr
     *
     * @access public
     * @return mixed
     */
    public function offsetExists($attr)
    {
        return $this->has($attr);
    }

    /**
     * offsetUnset
     *
     * @param mixed $attr
     *
     * @access public
     * @return void
     */
    public function offsetUnset($attr)
    {
        return $this->delete($attr);
    }

    /**
     * keys
     *
     * @access public
     * @return array
     */
    public function keys()
    {
        return array_keys($this->attributes);
    }
}
