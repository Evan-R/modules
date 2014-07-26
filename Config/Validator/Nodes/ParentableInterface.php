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
 * @class ChildAwareNodeInterface
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
interface ParentableInterface
{
    /**
     * hasChildren
     *
     *
     * @access public
     * @return boolean
     */
    public function hasChildren();

    /**
     * hasChild
     *
     * @param \Selene\Components\Config\Validator\Nodes\NodeInterface $node
     *
     * @access public
     * @return boolean
     */
    public function hasChild(NodeInterface $child);

    /**
     * addChildren
     *
     * @access public
     * @return void
     */
    public function addChild(NodeInterface $node);

    public function removeChild(NodeInterface $node);

    /**
     * getChildren
     *
     * @access public
     * @return array
     */
    public function getChildren();

    /**
     * getFirstChild
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface
     */
    public function getFirstChild();

    /**
     * getLastChild
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface
     */
    public function getLastChild();
}
