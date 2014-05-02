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
use \Jeremeamia\SuperClosure\SerializableClosure;

/**
 * @class ResolveFactoryClosuresPass
 * @package Selene\Components\DI\Resolver\Pass
 * @version $Id$
 */
class ResolveFactoryClosures implements ProcessInterface
{
    /**
     * process
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function process(ContainerInterface $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->hasFactory() && ($factory = $definition->getFactory()) instanceof \Closure) {
                throw new ContainerResolveException();
            }
        }
    }
}
