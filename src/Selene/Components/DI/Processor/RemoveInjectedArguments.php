<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Processor;

use \Selene\Components\DI\ContainerInterface;

/**
 * @class RemoveInjectedArguments implements ProcessInterface
 * @see ProcessInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RemoveInjectedArguments implements ProcessInterface
{
    /**
     * Remove arguments and setters from injected Services
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function process(ContainerInterface $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {

            if ($definition->isInjected()) {
                $definition->setArguments([]);
                $definition->setSetters([]);
            }
        }
    }
}
