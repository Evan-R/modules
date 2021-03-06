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

use \Selene\Module\DI\Reference;
use \Selene\Module\DI\CallerReference;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Definition\ServiceDefinition;
use \Selene\Module\DI\Definition\DefinitionInterface;

/**
 * Processes Service References on definition arguments
 *
 * @class ProcessArgumentReference implements ProcessInterface:w
 *
 * @see ProcessInterface
 *
 * @package Selene\Module\DI\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ResolveDefinitionArguments implements ProcessInterface
{
    private $current;
    private $container;

    private $building;

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
        $this->resolved = [];
        $this->container = $container;

        foreach ($container->getDefinitions() as $id => $definition) {
            $this->resolveDefinition($definition, $id);
        }
    }

    private function resolveDefinition(DefinitionInterface $definition, $id)
    {
        if (in_array($id, $this->resolved)) {
            return;
        }

        $this->current = $id = $this->container->getAlias($id);

        $this->resolved[] = $id;

        $this->replaceDefinitionArguments($definition);
        $this->replaceSetterArguments($definition);
        $this->replaceFactoryArguments($definition);
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
     * Adds the building class to the end of the argument list
     *
     * @param DefinitionInterface $definition
     *
     * @access private
     * @return mixed
     */
    private function replaceFactoryArguments(DefinitionInterface $definition)
    {
        if (!$definition->hasFactory()) {
            return;
        }

        $definition->addArgument($definition->getClass());
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
        $params = $this->container->getParameters();

        foreach ($arguments as $key => $argument) {
            $this->checkArgument($arg = $this->resolveArgument($argument));
            $args[$params->resolveParam($key)] = $arg;
        }

        return $args;
    }

    /**
     * checkArgument
     *
     * @return void
     */
    private function checkArgument($arg)
    {
        if (!($arg instanceof Reference) || $this->container->hasDefinition((string)$arg)) {
            return;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'A service "%s" requeired by "%s" does no exist.',
                $this->container->getAlias((string)$arg),
                $this->current
            )
        );
    }

    /**
     * resolveArgument
     *
     * @param mixed $argument
     *
     * @return mixed
     */
    private function resolveArgument($argument)
    {
        if (is_array($argument)) {
            return $this->resolveArguments($argument);
        }

        if (is_string($argument) && CallerReference::isReferenceString($argument)) {
            return CallerReference::fromString($argument);
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
