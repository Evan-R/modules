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
 * @class Children
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class Children implements \Countable, \IteratorAggregate
{
    /**
     * nodes
     *
     * @var array
     */
    private $nodes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->nodes = [];
    }

    /**
     * attach
     *
     * @param NodeInterface $node
     *
     * @return void
     */
    public function attach(NodeInterface $node)
    {
        $this->nodes[$hash = spl_object_hash($node)] = &$node;
    }

    /**
     * detach
     *
     * @param NodeInterface $node
     *
     * @return void
     */
    public function detach(NodeInterface $node)
    {
        if ($this->has($node)) {
            unset($this->nodes[spl_object_hash($node)]);
        }
    }

    /**
     * detachAll
     *
     * @return void
     */
    public function detachAll()
    {
        $this->nodes = [];
    }

    /**
     * first
     *
     * @return NodeInterface|null
     */
    public function first()
    {
        if (0 < $this->count()) {
            return current($this->nodes);
        }
    }

    /**
     * last
     *
     * @return NodeInterface|null
     */
    public function last()
    {
        if (0 < ($count = $this->count())) {
            return end($this->nodes);
        }
    }

    /**
     * has
     *
     * @param NodeInterface $node
     *
     * @return boolean
     */
    public function has(NodeInterface $node)
    {
        return isset($this->nodes[spl_object_hash($node)]);
    }

    /**
     * find
     *
     * @param \Closure $callback
     *
     * @return NodeInterface|null
     */
    public function find(\Closure $callback)
    {
        $results = $this->filter($callback);

        return $results ? current($results) : null;
    }

    /**
     * filter
     *
     * @param \Closure $filter
     *
     * @return array
     */
    public function filter(\Closure $filter)
    {
        return array_filter($this->nodes, $filter);
    }

    /**
     * getIterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_values($this->nodes));
    }

    /**
     * count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->nodes);
    }

}
