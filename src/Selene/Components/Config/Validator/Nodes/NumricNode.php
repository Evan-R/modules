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
 * @class NumricNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
abstract class NumricNode extends ScalarNode
{
    protected $min;

    protected $max;

    /**
     * range
     *
     * @param mixed $min
     * @param mixed $max
     *
     * @access public
     * @return mixed
     */
    public function range($min, $max)
    {
        $this->min($min);
        return $this->max($min);
    }

    public function max($max)
    {
        $this->max = $max;
        return $this;
    }

    public function min($min)
    {
        $this->min = $min;
        return $this;
    }
}
