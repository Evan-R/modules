<?php

/**
 * This File is part of the Selene\Module\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

/**
 * @class UnsetValue
 * @package Selene\Module\Config\Validator\Nodes
 * @version $Id$
 */
class UnsetValue
{
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function getValue()
    {
        return null;
    }
}
