<?php

/**
 * This File is part of the Selene\Components\Config\Validation\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Nodes;

use \Selene\Components\Config\Validation\Builder;

/**
 * @class ArrayNode
 * @package Selene\Components\Config\Validation\Nodes
 * @version $Id$
 */
class ArrayNode extends Node implements \IteratorAggregate, \Countable
{

    protected $children;

    public function __construct(Builder $builder, $name)
    {
        $this->children = [];
        parent::__construct($builder, $name);
    }

    /**
     * getIterator
     *
     * @access public
     * @return mixed
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    public function count()
    {
        return count($this->children);
    }

    public function keys()
    {
        return array_keys($this->children);
    }


    /**
     * scalar
     *
     * @param mixed $name
     *
     * @access public
     * @return Node
     */
    public function scalarNode($name)
    {
        if ($this->isClosed()) {
            throw new \Exception('Can\'t add a ScalarNode to a closed node');
        }

        $this->children[$name] = $node = $this->builder->scalarNode($name);
        return $node;
    }

    /**
     * arrayNode
     *
     * @access public
     * @return mixed
     */
    public function arrayNode($name)
    {
        if ($this->isClosed()) {
            throw new \Exception('Can\'t add an ArrayNode to a closed node');
        }

        $this->children[$name] = $node = $this->builder->arrayNode($name);
        return $node;
    }

    public function childNodes()
    {
        return $this->children;
    }
}
