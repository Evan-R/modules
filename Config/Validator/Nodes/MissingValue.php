<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

/**
 * @class MissingValue
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class MissingValue
{
    private $node;

    /**
     * Constructor
     *
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    public function getValue()
    {
        return null;
    }

    public function getNode()
    {
        return $this->node;
    }
}
