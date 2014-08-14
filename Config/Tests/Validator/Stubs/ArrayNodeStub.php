<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Validator\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Validator\Stubs;

use \Selene\Module\Config\Validator\Nodes\ArrayNode;

/**
 * @class ArrayNodeStub
 * @package Selene\Module\Config\Tests\Validator\Stubs
 * @version $Id$
 */
class ArrayNodeStub extends ArrayNode
{
    public function validateType($type = null)
    {
        return true;
    }

    protected function checkExceedingKeys(array $value)
    {
    }
}
