<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config;

use \Selene\Module\DI\BuilderInterface;

/**
 * @class ConfigrurationInterface
 * @package
 * @version $Id$
 */
interface ConfigurationInterface
{
    public function load(BuilderInterface $builder, array $values);

    public function validate(array $config);
}
