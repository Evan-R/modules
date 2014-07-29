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
 * @class RootNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class RootNode extends DictNode implements RootNodeInterface
{
    /**
     * Always return false
     *
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException every time it is called
     * @access public
     * @return void
     */
    public function setParent(NodeInterface $node)
    {
        $parent = $node->getKey() ?: 'parent node';
        $root   = $this->getKey() ?: 'current node';

        throw new \BadMethodCallException(
            sprintf('cannot set %s as parent of %s, since %s is root', $parent, $root, $root)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException every time it is called
     * @access public
     * @return void
     */
    public function getParent()
    {
        throw new \BadMethodCallException(
            sprintf('root node %s has no parent', $this->getKey())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finalize($value = null)
    {
        $this->value = (array)$value;
        $this->finalized = true;
    }

    ///**
    // * {@inheritdoc}
    // */
    //public function validate()
    //{
    //    $results = [];
    //    $values  = $this->getValue();

    //    foreach ($this->getChildren() as $child) {

    //        $key = $child->getKey();

    //        $child->finalize(array_key_exists($key, $values) ? $values[$key] : null);
    //        $child->validate();

    //        $results[$key] = $child->getValue();
    //    }

    //    $this->value = $results;
    //}
}
