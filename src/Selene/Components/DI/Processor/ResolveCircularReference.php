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

use \Selene\Components\DI\Definition;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\DefinitionInterface;
use \Selene\Components\DI\Exception\CircularReferenceException;

/**
 * @class ResolveCircularReference implements ProcessInterface
 * @see ProcessInterface
 *
 * @package Selene\Components\DI\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ResolveCircularReference implements ProcessInterface
{
    private $container;

    /**
     * resolve
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        foreach ($container->getDefinitions() as $id => $definition) {
            // requiring the service in a setter argument is fine:
            $this->checkDefininition($definition, $id, $id);
        }
    }

    /**
     * checkDefininition
     *
     * @param DefinitionInterface $definition
     * @param mixed $current
     *
     * @access private
     * @return void
     */
    private function checkDefininition(DefinitionInterface $definition, $current, $self = null)
    {
        $this->checkCircularReference($definition->getArguments(), $current);

        $this->checkSetterArguments((array)$definition->getSetters(), $current, $self);
    }

    /**
     * checkCircularReference
     *
     * @param array $attributes
     * @param void $current
     * @param void $self
     *
     * @access protected
     * @return void
     */
    protected function checkCircularReference(array $attributes, $current, $self = null)
    {
        foreach ($attributes as $attribute) {

            if (is_array($attribute)) {
                $this->checkCircularReference($attribute, $current, $self);
                continue;
            }

            if (!$this->container->isReference($attribute)) {
                continue;
            }

            if ($self === ($id = $attribute->get()) && null !== $self) {
                continue;
            }

            if ($current === $id) {
                throw new CircularReferenceException(
                    sprintf('Service \'%s\' has circular reference on \'%s\'', $current, $id)
                );
            }

            $this->checkDefininition($this->container->getDefinition($id), $current, $id, $self);
        }
    }

    /**
     * checkSetterArguments
     *
     * @param array $setters
     * @param mixed $current
     * @param mixed $self
     *
     * @access private
     * @return void
     */
    private function checkSetterArguments(array $setters, $current, $self = null)
    {
        foreach ($setters as $setter) {

            $method = key($setter);
            $arguments = $setter[$method];

            $this->checkCircularReference($arguments, $current, $self);
        }
    }
}
