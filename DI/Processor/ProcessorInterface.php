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
 * @interface ProcessorInterface
 * @package Selene\Module\DI\Processor
 * @version $Id$
 */
interface ProcessorInterface
{
    const BEFORE_OPTIMIZE = 0;

    const OPTIMIZE = 1;

    const BEFORE_REMOVE = 2;

    const RESOLVE = 3;

    const REMOVE = 5;

    const AFTER_REMOVE = 6;

    public function process(ContainerInterface $container);

    public function add(ProcessInterface $process, $priority = ProcessorInterface::BEFORE_OPTIMIZE);
}
