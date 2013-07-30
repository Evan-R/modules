<?php

/**
 * This File is part of the Selene\Components\DependencyInjection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection\Tests\Stubs;

use Selene\Components\DependencyInjection\Container;

/**
 * @class ExtendedContainer
 * @package
 * @version $Id$
 */
class ExtendedContainer extends Container
{
    protected function getFooBarBinding()
    {
        return new Foo;
    }

    protected function getFooDotBarDashBazBinding()
    {
        return new Foo;
    }

    protected function getBarBinding()
    {
        return new Bar;
    }
}

/**
 * Class: Foo
 *
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Foo
{}

/**
 * Class: Bar
 *
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Bar
{}
