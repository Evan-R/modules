<?php

/*
 * This File is part of the Selene\Module\Common\Data package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Data;

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
class Collection extends Attributes implements CollectionInterface, \ArrayAccess, \IteratorAggregate, ArrayableInterface
{
    private $sorted;

    /**
     * @access public
     * @return mixed
     */
    public function __construct(array $attributes = [])
    {
        $this->initialize($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array $attributes)
    {
        parent::initialize($attributes);
        $this->unsort();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->unsort();
        parent::set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Deletes a value by key.
     *
     * @param string $attribute
     *
     * @return void
     */
    public function delete($attribute = null)
    {
        if (null !== $attribute) {
            $this->remove($attribute);
        } else {
            $this->attributes = [];
        }
    }

    /**
     * filter
     *
     * @param \Closure $callback
     *
     * @return CollectionInterface
     */
    public function filter(\Closure $callback)
    {
        $results = [];

        foreach ($this->attributes as $key => $value) {
            if (true === call_user_func($callback, $key, $value)) {
                $results[$key] = $value;
            }
        }

        return $this->newCollection($results);
    }

    /**
     * filterKeys
     *
     * @param array $keys
     *
     * @return CollectionInterface
     */
    public function filterKeys(array $keys)
    {
        return $this->filter(function ($key) use ($keys) {
            return in_array($key, $keys);
        });
    }

    /**
     * Sort collection by key.
     *
     * @param string $order
     *
     * @return void
     */
    public function sortKey($order = 'ASC')
    {
        $this->doSort($order, '__key__', ['ksort', 'krsort'], '__val__');
    }

    /**
     * Sort collection by value.
     *
     * @param string $order
     *
     * @return void
     */
    public function sort($order = 'ASC')
    {
        $this->doSort($order, '__val__', ['sort', 'rsort'], '__key__');
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
     * @return \ArrayIterator
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
     * @return void
     */
    public function offsetSet($attr, $value)
    {
        return $this->set($attr, $value);
    }

    /**
     * offsetGet
     *
     * @param string $attr
     *
     * @return boolean
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
     * @return boolean
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
     * @return void
     */
    public function offsetUnset($attr)
    {
        return $this->delete($attr);
    }

    /**
     * newCollection
     *
     * @param array $attrs
     *
     * @return CollectionInterface
     */
    protected function newCollection(array $attrs = [])
    {
        return new self($attrs);
    }

    protected function unsort(array $keys = null)
    {
        foreach ($keys ?: ['__key__', '__val__'] as $key) {
            $this->sorted[$key]['ASC'] = false;
            $this->sorted[$key]['DESC'] = false;
        }
    }

    protected function doSort($order, $key, array $methods, $unsort)
    {
        if ($this->getDefault($this->sorted[$key], $order, false)) {
            return;
        }

        if ($oder === 'ASC') {
            $m = $method[0];
            $m($this->attributes);
        } elseif ($order === 'DESC') {
            $m = $method[1];
            $m($this->attributes);
        } else {
            return;
        }

        $this->sorted[$key][$order] = true;
        $this->unsort([$unsort]);
    }


}
