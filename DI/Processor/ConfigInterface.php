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

/**
 * @interface ConfigInterface
 * @package Selene\Components\DI\Processor
 * @version $Id$
 */
interface ConfigInterface
{
    public function setConfig(array $config);

    public function getConfig();

    public function mergeConfig(array $config);
}
