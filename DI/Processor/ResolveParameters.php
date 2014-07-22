<?php

/**
 * This File is part of the Selene\Components\DI\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Processor;

use \Selene\Components\DI\ContainerInterface;

/**
 * @class ResolveParameters
 * @package Selene\Components\DI\Processor
 * @version $Id$
 */
class ResolveParameters implements ProcessInterface
{
    public function process(ContainerInterface $container)
    {
        $container->getParameters()->resolve();
    }
}
