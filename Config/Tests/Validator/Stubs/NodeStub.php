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

use \Selene\Module\Config\Validator\Nodes\ScalarNode as AbstractNode;

/**
 * @class NodeStub
 * @package Selene\Module\Config\Tests\Validator\Stubs
 * @version $Id$
 */
class NodeStub extends AbstractNode
{
    /**
     * validateType
     *
     * @param mixed $type
     *
     * @access public
     * @return mixed
     */
    public function validateType($type = null)
    {
        return true;
    }
}
