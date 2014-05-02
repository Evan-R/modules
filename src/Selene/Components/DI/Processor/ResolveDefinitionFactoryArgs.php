<?php

/**
 * This File is part of the Selene\Components\DI\Resolver\Pass package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Processor;

use \Selene\Components\DI\ContainerInterface;

/**
 * @class ResolveDefinitionFactoryArgs implements ProcessInterface
 * @see ProcessInterface
 *
 * @package Selene\Components\DI\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ResolveDefinitionFactoryArgs implements ProcessInterface
{
    public function process(ContainerInterface $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->hasFactory()) {
                $args = $definition->getArguments();
                $class = $definition->getClass();
                array_unshift($args, $class);
                $definition->setArguments($args);
            }
        }
    }
}
