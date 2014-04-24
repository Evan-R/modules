<?php

/**
 * This File is part of the Selene\Components\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

/**
 * @abstract class ArrayNode extends Node implements ParentableInterface, \IteratorAggregate
 * @see ParentableInterface
 * @see \IteratorAggregate
 * @see Node
 * @abstract
 *
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class ArrayNode extends Node implements ParentableInterface
{
    /**
     * children
     *
     * @var array
     */
    protected $children;

    /**
     * getChildren
     *
     * @access public
     * @return array
     */
    public function getChildren()
    {
        return $this->children ?: [];
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
     * getKeys
     *
     * @access public
     * @return array
     */
    public function getKeys()
    {
        $keys = [];

        foreach ($this->getChildren() as $child) {
            $keys[] = $child->getKey();
        }

        return $keys;
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
            return $node->setParent($this);
        }

        $this->children[] = $node;

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
    public function getChildByKey($key)
    {
        $children = array_filter($this->getChildren(), function ($child) use ($key) {
            return $child->getKey() === $key;
        });

        return (bool)$children ? current($children) : null;
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
}
