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
 * @class NumericNode extends ScalarNode
 * @see ScalarNode
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class NumericNode extends ScalarNode
{
    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        return is_numeric($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('%s needs to be numeric', $this->getKey());
    }
}
