<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\ContainerInterface;

/**
 * @class ConfigrurationInterface
 * @package
 * @version $Id$
 */
interface ConfigurationInterface
{
    public function load(BuilderInterface $builder, array $values);
}
