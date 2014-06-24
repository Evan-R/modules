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
 * @class ProcessInterface
 * @package Selene\Components\DI\Processor
 * @version $Id$
 */
interface ProcessInterface
{
    const BEFORE_RESOLVE = 1;

    const AFTER_RESOLVE = 2;

    public function process(ContainerInterface $container);
}
