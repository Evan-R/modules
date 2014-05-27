<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Stubs;

use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class ContainerAwareStub
 * @package Selene\Components\DI\Tests\Stubs
 * @version $Id$
 */
class ContainerAwareStub implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
