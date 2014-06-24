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
 * @interface NodeInterface
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface NodeInterface
{
    public function setBuilder($builder);

    /**
     * setParent
     *
     * @param \Selene\Components\Config\Validator\Nodes\NodeInterface $node
     *
     * @access public
     * @return void
     */
    public function setParent(NodeInterface $node);

    /**
     * getParent
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface
     */
    public function getParent();

    /**
     * hasParent
     *
     *
     * @access public
     * @return boolean
     */
    public function hasParent();
}
