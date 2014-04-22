<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

/**
 * @abstract class Node implements NodeInterface
 * @see NodeInterface
 * @abstract
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Node implements NodeInterface
{
    /**
     * parent
     *
     * @var NodeInterface
     */
    protected $parent;

    /**
     * children
     *
     * @var array
     */
    protected $children;

    /**
     * @param NodeInterface $parent
     *
     * @access public
     */
    public function __construct(NodeInterface $parent = null)
    {
        $this->children = [];
        $this->parent = $parent ? $this->setParent($parent) : $parent;
    }

    /**
     * setParent
     *
     * @param NodeInterface $node
     *
     * @access public
     * @return NodeInterface this instance
     */
    public function setParent(NodeInterface $node)
    {
        $this->parent = $node;
        $node->addChild($this);
        return $this;
    }

    /**
     * getParent
     *
     * @access public
     * @return void
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * hasParent
     *
     * @access public
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * getChildren
     *
     * @access public
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * hasChildren
     *
     * @access public
     * @return boolean
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * getFirstChild
     *
     * @access public
     * @return Nodeinterface
     */
    public function getFirstChild()
    {
        if ($this->hasChildren()) {
            return current($this->children);
        }
    }

    /**
     * getLastChild
     *
     * @access public
     * @return NodeInterface
     */
    public function getLastChild()
    {
        if ($this->hasChildren()) {
            return end($this->children);
        }
    }

    /**
     * addChild
     *
     * @param NodeInterface $node
     *
     * @access public
     * @return NodeInterface this instance
     */
    public function addChild(NodeInterface $node)
    {
        if ($node->getParent() !== $this) {
            $node->setParent($this);
        } else {
            $this->children[] = $node;
        }

        return $this;
    }

    /**
     * hasChild
     *
     * @param NodeInterface $child
     *
     * @access public
     * @return mixed
     */
    public function hasChild(NodeInterface $child)
    {
        $found = array_filter($this->children, function ($c) use ($child) {
            return $c === $child;
        });

        return (bool)$found;
    }

    /**
     * __call
     *
     * @param mixed $method
     * @param mixed $arguments
     *
     * @access public
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($class = class_exists(__NAMESPACE__.'\\'.ucfirst(strtolower($method).'Node'))) {
            array_unshift($arguments, $this);
            return (new \ReflectionClass($class))->newInstanceArgs($arguments);
        }
    }
}
