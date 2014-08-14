<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Processor;

use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\ParameterInterface;
use \Selene\Module\DI\Definition\DefinitionInterface;

/**
 * @class ResolveDefinitionDependencies implements ProcessInterface
 * @see ProcessInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ResolveDefinitionDependencies implements ProcessInterface
{

    /**
     * Resolves `Class` and `File` parameters on definitions.
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function process(ContainerInterface $container)
    {
        $parameters = $container->getParameters();

        foreach ($container->getDefinitions() as $id => $definition) {
            $this->checkFileIntegrity($definition, $parameters, $id);
            $this->checkClassIntegrity($definition, $parameters, $id);
        }
    }

    /**
     * Set the resolve class string on the service definition.
     *
     * @param DefinitionInterface $definition
     *
     * @throws \InvalidArgumentException if the class does not exist.
     * @return void
     */
    private function checkClassIntegrity(DefinitionInterface $definition, ParameterInterface $parameters, $id)
    {
        if ($definition->requiresFile()) {
            include_once $definition->getFile();
        }

        $class = $parameters->resolveParam($definition->getClass());

        if (!$class || (0 < strlen($class) && !class_exists($class))) {
            throw $this->getClassException($class, $id);
        }

        $definition->setClass($class);

        $this->checkServiceFactory($definition, $parameters, $id);
    }

    /**
     * checkServiceFactory
     *
     * @param DefinitionInterface $definition
     *
     * @throws \InvalidArgumentException if the class does not exist.
     * @return void
     */
    private function checkServiceFactory(DefinitionInterface $definition, ParameterInterface $parameters, $id)
    {
        if (!$definition->hasFactory()) {
            return;
        }

        list ($class, $method) = $this->getFactoryCallback($parameters->resolveParam($definition->getFactory()));

        if ($class && !class_exists($class)) {
            throw $this->getClassException($class, $id);
        }

        if (!$class && !is_callable($method) || ($class && !(new \ReflectionClass($class))->hasMethod($method))) {
            throw new \InvalidArgumentException(
                sprintf('Service factory for service "%s" requires a valid callback', $id)
            );
        }

        $definition->setFactory($class, $method);
    }

    /**
     * Extract class and method from a factoty parameter.
     *
     * @param mixed $factory
     *
     * @return array
     */
    private function getFactoryCallback($factory)
    {
        if (is_array($factory)) {
            return $factory;
        }
        return array_pad(explode('::', $factory), -2, null);
    }

    /**
     * throwClassException
     *
     * @param mixed $class
     * @param mixed $id
     *
     * @return \InvalidArgumentException
     */
    private function getClassException($class, $id)
    {
        return new \InvalidArgumentException(
            sprintf('class "%s" required by service "%s" does not exist', $class, $id)
        );
    }

    /**
     * Set the resolve file string on the service definition.
     *
     * @param DefinitionInterface $definition
     *
     * @throws \InvalidArgumentException if the file does not exist
     *
     * @return void
     */
    private function checkFileIntegrity(DefinitionInterface $definition, ParameterInterface $parameters, $id)
    {
        if (!$definition->requiresFile()) {
            return;
        }

        if (!is_file($file = $parameters->resolveParam($definition->getFile()))) {
            throw new \InvalidArgumentException(
                sprintf('file "%s" required by service "%s" does not exist', $file, $id)
            );
        }

        $definition->setFile($file);
    }
}
