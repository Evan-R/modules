<?php

/**
 * This File is part of the Selene\Module\DI\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Processor;

use \Selene\Module\DI\ContainerInterface;

/**
 * @class ProcessInterface
 * @package Selene\Module\DI\Processor
 * @version $Id$
 */
interface ProcessInterface
{
    const BEFORE_RESOLVE = 1;

    public function process(ContainerInterface $container);
}
