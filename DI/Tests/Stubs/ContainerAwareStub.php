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

use \Selene\Module\DI\ContainerAwareInterface;
use \Selene\Module\DI\Traits\ContainerAwareTrait;

/**
 * @class ContainerAwareStub
 * @package Selene\Module\DI\Tests\Stubs
 * @version $Id$
 */
class ContainerAwareStub implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
