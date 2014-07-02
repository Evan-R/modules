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
 * @interface ProcessorInterface
 * @package Selene\Components\DI\Processor
 * @version $Id$
 */
interface ProcessorInterface
{
    const BEFORE_OPTIMIZE = 0;

    const OPTIMIZE = 1;

    const BEFORE_REMOVE = 2;

    const REMOVE = 3;

    const AFTER_REMOVE = 4;

    public function process(ContainerInterface $container);

    public function add(ProcessInterface $process, $priority = ProcessorInterface::BEFORE_OPTIMIZE);
}
