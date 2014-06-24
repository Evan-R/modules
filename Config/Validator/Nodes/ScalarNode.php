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
 * @class Scalar
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
abstract class ScalarNode extends Node
{
    protected $type;

    /**
     * @param NodeInterface $parent
     *
     * @access public
     * @return mixed
     */
    public function __construct($type = null)
    {
        $this->type = $type;
        $this->required = true;
    }
}
