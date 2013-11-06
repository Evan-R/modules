<?php

/**
 * This File is part of the Selene\Components\DependencyInjection\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection\Tests\Stubs;

use Selene\Components\DependencyInjection\Container;
use Selene\Components\DependencyInjection\Parameters;

/**
 * @class LockedContainerStub
 * @package Selene\Components\DependencyInjection\Tests\Stubs
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
