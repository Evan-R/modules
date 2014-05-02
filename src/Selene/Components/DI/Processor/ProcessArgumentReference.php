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

use \Selene\Components\DI\Refetence;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;

/**
 * Processes Service References on definition arguments
 *
 * @class ProcessArgumentReference implements ProcessInterface:w
 *
 * @see ProcessInterface
 *
 * @package Selene\Components\DI\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ProcessArgumentReference implements ProcessInterface
{
    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        foreach ($container->getDefinitions() as $definition) {
            $this->replaceDefinitionArguments($definition);
            $this->replaceSetterArguments($definition);
        }
    }

    private function replaceDefinitionArguments($definition)
    {
        foreach ($definition->getArguments() as $index => $argument) {
            if ($this->isDefinitionString($argument)) {
                $definition->replaceArgument(new Reference(substr($argument, 1)), $index);
            }
        }
    }

    /**
     * replaceSetterArguments
     *
     * @param mixed $definition
     *
     * @access private
     * @return void
     */
    private function replaceSetterArguments($definition)
    {

        if (!$definition->hasSetters()) {
            return;
        }

        $setters = [];

        foreach ($definition->getSetters() as $setter) {

            $args = [];
            $method = key($setter);
            $arguments = $setter[$method];

            foreach ($arguments as $argument) {
                if ($this->isDefinitionString($argument)) {
                    $args[] = new Reference(substr($argument, 1));
                } else {
                    $args[] = $argument;
                }
            }

            $setters[] = [$method => $args];
        }

        $definition->setSetters($setters);
    }


    /**
     * isDefinitionString
     *
     * @param mixed $def
     *
     * @access private
     * @return boolea
     */
    private function isDefinitionString($argument)
    {
        return is_string($argument) &&
            (
                0 === strpos($argument, '$') && $this->container->hasDefinition(substr($argument, 1))
            );
    }
}
