<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

/**
 * @abstract class ArrayNode extends Node implements ParentableInterface
 * @see ParentableInterface
 * @see \IteratorAggregate
 * @see Node
 * @abstract
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class ArrayNode extends Node implements ParentableInterface, \IteratorAggregate
{
    /**
     * children
     *
     * @var SplObjectStorage
     */
    protected $children;

    /**
     * type
     *
     * @var string
     */
    protected $type = self::T_ARRAY;

    /**
     * Constructor.
     *
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node = null)
    {
        $this->children = new Children;
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        parent::__clone();

        $children = $this->getChildren();
        $this->children = new Children;

        foreach ($children as $child) {
            $this->addChild(clone($child));
        }
    }

    /**
     * Get all child nodes
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * unsetValue
     *
     * @param string $key
     *
     * @return void
     */
    public function unsetValue($key = null)
    {
        if (null !== $key) {
            $this->value = null;
        } else {
            unset($this->value[$key]);
        }
    }

    /**
     * Check it this node has children
     *
     * @access public
     * @return boolean
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * Get the first child node
     *
     * @access public
     * @return Nodeinterface
     */
    public function getFirstChild()
    {
        if ($this->hasChildren()) {
            return $this->children->first();
        }
    }

    /**
     * Get the last child node
     *
     * @access public
     * @return NodeInterface
     */
    public function getLastChild()
    {
        if ($this->hasChildren()) {
            return $this->children->last();
        }
    }

    /**
     * Get all keys of all childnodes.
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
     * Adds a childnode.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface this instance
     */
    public function addChild(NodeInterface $node)
    {
        if ($node->getParent() !== $this) {
            return $node->setParent($this);
        }

        if (!$this->hasChild($node)) {
            $this->children->attach($node);
        }

        return $this;
    }

    /**
     * removeChild
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function removeChild(NodeInterface $node)
    {
        if ($this->hasChild($node)) {
            $node->removeParent();
            $this->children->detach($node);
        }

        return $this;
    }

    /**
     * Check if a node is a child.
     *
     * @param NodeInterface $node
     *
     * @return boolean
     */
    public function hasChild(NodeInterface $child)
    {
        return $this->children->has($child);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        parent::validate();

        $results = [];
        $values = $this->getValue();

        if (0 !== count($this->children)) {
            $this->validateChildren((array)$values);
        }

        return true;
    }

    /**
     * validateChildren
     *
     * @param array $values
     * @param array $results
     *
     * @return void
     */
    protected function validateChildren(array $values, array &$results = [])
    {
        foreach ($this->getChildren() as $child) {

            $value = array_key_exists($child->getKey(), $values) ? $values[$child->getKey()] : new MissingValue($child);

            $child->finalize($value);

            // child could be removed:
            if (!$this->hasChild($child)) {
                continue;
            }

            $child->validate();

            $results[$child->getKey()] = $child->getValue();
        }

        $this->value = $this->mergeValues($values, $results);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->getChildren();
    }

    /**
     * mergeValues
     *
     * @param array $values
     * @param array $results
     *
     * @return array
     */
    protected function mergeValues(array $values, array $results)
    {
        return array_merge($values, $results);
    }
}
