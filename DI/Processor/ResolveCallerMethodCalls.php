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

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\DefinitionInterface;

/**
 * @class ResolveCallerMethodCalls extends Process
 * @see Process
 *
 * @package Selene\Components\DI\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ResolveCallerMethodCalls implements ProcessInterface
{
    private $container;

    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->hasSetters()) {
                $this->checkSetterIntegrity($definition, $id);
            }
        }
    }

    /**
     * checkSetterIntegrity
     *
     * @param DefinitionInterface $definition
     *
     * @throws \InvalidArgumentException
     * @access private
     * @return void
     */
    private function checkSetterIntegrity(DefinitionInterface $definition, $id)
    {
        $reflection = new \ReflectionClass($definition->getClass());

        foreach ($definition->getSetters() as $setter) {
            if (!$reflection->hasMethod($method = key($setter))) {
                throw $this->getSetterException($reflection, $method, $id);
            }
        }
    }

    /**
     * handleSetterException
     *
     * @param \ReflectionClass $class
     * @param string $method
     * @param string $id
     *
     * @access private
     * @return void
     */
    private function getSetterException(\ReflectionClass $class, $method, $id)
    {
        return new \InvalidArgumentException(
            sprintf(
                'Service "%s" is configured to call "%s" on %s. This method does not exist.',
                $id,
                $method,
                $class->getName()
            )
        );
    }
}
