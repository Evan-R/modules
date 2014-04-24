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
 * @class NumericNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class NumericNode extends ScalarNode
{
    public function validateType($value)
    {
        return is_numeric($value);
    }

    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('%s needs to be numeric', $this->getKey());
    }
}
