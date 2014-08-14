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

/**
 * @interface ConfigInterface
 * @package Selene\Module\DI\Processor
 * @version $Id$
 */
interface ConfigInterface
{
    public function setConfig(array $config);

    public function getConfig();

    public function mergeConfig(array $config);
}
