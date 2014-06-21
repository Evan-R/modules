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

use \Selene\Components\DI\Reference;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;
use \Selene\Components\DI\Definition\DefinitionInterface;

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
class ResolveDefinitionArguments implements ProcessInterface
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
        $this->container = $container;

        foreach ($container->getDefinitions() as $definition) {
            $this->replaceDefinitionArguments($definition);
            $this->replaceSetterArguments($definition);
            $this->replaceFactoryArgsuments($definition);
        }
    }

    /**
     * replaceDefinitionArguments
     *
     * @param DefinitionInterface $definition
     *
     * @access private
     * @return void
     */
    private function replaceDefinitionArguments(DefinitionInterface $definition)
    {
        $arguments = $this->resolveArguments($definition->getArguments());

        $definition->setArguments($arguments);
    }

    /**
     * replaceSetterArguments
     *
     * @param mixed $definition
     *
     * @access private
     * @return void
     */
    private function replaceSetterArguments(DefinitionInterface $definition)
    {

        if (!$definition->hasSetters()) {
            return;
        }

        $setters = [];


        foreach ($definition->getSetters() as $key => $setter) {
            $method = key($setter);
            $arguments = $setter[$method];

            $setters[$key] = [$method => $args = $this->resolveArguments($arguments)];
        }

        $definition->setSetters($setters);
    }

    /**
     * replaceFactoryArgsuments
     *
     * @param DefinitionInterface $definition
     *
     * @access private
     * @return mixed
     */
    private function replaceFactoryArgsuments(DefinitionInterface $definition)
    {
        if (!$definition->hasFactory()) {
            return;
        }

        $arguments =  $definition->getArguments();

        array_unshift($arguments, $definition->getClass());

        $definition->setArguments($arguments);
    }


    /**
     * resolveArguments
     *
     * @param array $argument
     *
     * @access private
     * @return array
     */
    private function resolveArguments(array $arguments)
    {
        $args = [];

        foreach ($arguments as $key => $argument) {
            $args[$key] = $this->resolveArgument($argument);
        }

        return $args;
    }

    /**
     * resolveArgument
     *
     * @param mixed $argument
     *
     * @access private
     * @return mixed
     */
    private function resolveArgument($argument)
    {
        if (is_array($argument)) {
            return $this->resolveArguments($argument);
        }

        if ($this->isDefinitionString($argument)) {
            return new Reference(substr($argument, strlen(ContainerInterface::SERVICE_REF_INDICATOR)));
        }

        if (is_scalar($argument)) {
            return $this->container->getParameters()->resolveParam($argument);
        }

        return $argument;
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
