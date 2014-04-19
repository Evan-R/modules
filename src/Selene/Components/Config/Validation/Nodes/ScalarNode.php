<?php

/**
 * This File is part of the Selene\Components\Config\Validation\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Nodes;

/**
 * @class Scalar
 * @package Selene\Components\Config\Validation\Nodes
 * @version $Id$
 */
class ScalarNode extends Node
{
    const TYPE_BOOLEAN = 1;
    const TYPE_FLOAT = 2;
    const TYPE_INTEGER = 3;
    const TYPE_STRING = 4;

    protected $scalarType;

    public function boolean()
    {
        $this->scalarType = static::TYPE_BOOLEAN;
        return $this;
    }

    public function float()
    {
        $this->scalarType = static::TYPE_FLOAT;
        return $this;
    }

    public function integer()
    {
        $this->scalarType = static::TYPE_INTEGET;
        return $this;
    }

    public function string()
    {
        $this->scalarType = static::TYPE_STRING;
        return $this;
    }

    public function getType()
    {
        return $this->scalarType;
    }
}
