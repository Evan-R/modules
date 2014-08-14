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

use \Selene\Module\Config\Validator\Builder;

/**
 * @interface NodeInterface
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface NodeInterface
{
    const T_BOOL    = 'boolean';
    const T_FLOAT   = 'float';
    const T_INTEGER = 'integer';
    const T_STRING  = 'string';
    const T_ARRAY   = 'array';

    /**
     * Set the validation builder.
     *
     * @param Builder $builder
     *
     * @return NodeInterface
     */
    public function setBuilder(Builder $builder);

    /**
     * Set the parent node.
     *
     * @param NodeInterface $node the parent node
     *
     * @return NodeInterface
     */
    public function setParent(NodeInterface $node);

    /**
     * Get the parent node.
     *
     * @return NodeInterface
     */
    public function getParent();

    /**
     * Check if the node as a parent.
     *
     * @return boolean
     */
    public function hasParent();

    /**
     * getType
     *
     * @return string
     */
    public function getType();

    /**
     * Start a if/then condition block on the node.
     *
     * @return NodeInterface
     */
    public function condition();

    /**
     * Mark the node optional.
     *
     * @return NodeInterface
     */
    public function optional();

    /**
     * Mark the node not to be empty.
     *
     * @return NodeInterface
     */
    public function notEmpty();

    /**
     * Validate the node.
     *
     * @return boolean
     */
    public function validate();

    /**
     * finalize
     *
     * @param mixed $value
     *
     * @return NodeInterface
     */
    public function finalize($value = null);
}
