<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Stubs;

use Selene\Module\DI\Container;
use Selene\Module\DI\Parameters;

/**
 * @class LockedContainerStub
 * @package Selene\Module\DI\Tests\Stubs
 * @version $Id$
 */
class LockedContainerStub extends Container
{
    protected $locked;

    public function __construct(ParameterInterface $parameters = null)
    {
        parent::__construct($parameters);
        $this->locked = true;
    }
}
