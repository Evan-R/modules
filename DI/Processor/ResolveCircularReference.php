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

use \Selene\Components\DI\Definition\CallerDefinition;
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
    /**
     * container
     *
     * @var ContainerInterface
     */
    private $container;

    private $current;

    /**
     * Resolve circular references among service definitions.
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        $aliases = $this->container->getAliases();

        foreach ($container->getDefinitions() as $id => $definition) {
            // requiring the service in a setter argument is fine:
            $this->checkDefininition($definition, $id, $id);
        }
    }

    /**
     * Check a definition's arguemnts and setters.
     *
     * @param DefinitionInterface $definition
     * @param mixed $current
     *
     * @access private
     * @return void
     */
    private function checkDefininition(DefinitionInterface $definition, $current, $self = null)
    {
        $this->checkCircularReference($definition->getArguments(), $current, $self);

        $this->checkSetterArguments((array)$definition->getSetters(), $current, $self);
    }

    /**
     * Check if an attribute is a reference and matches the current resolving
     * service definition.
     *
     * @param array $attributes
     * @param string $current
     * @param string $self
     *
     * @throws \Selene\Components\DI\Exception\CircularReferenceException if
     * a circular reference occurred.
     * @access protected
     * @return void
     */
    protected function checkCircularReference(array $attributes, $current, $self = null)
    {
        $current = $this->container->getAlias($current);
        $self    = $self ? $this->container->getAlias($self) : null;

        foreach ($attributes as $attribute) {

            if (is_array($attribute)) {
                $this->checkCircularReference($attribute, $current, $self);
                continue;
            }

            if ($attribute instanceof CallerDefinition) {
                $this->checkCircularReference([$attribute->getService()], $current, $self);
                continue;
            }

            if (!$this->container->isReference($attribute)) {
                continue;
            }

            // if all tests passes, continue to check the definitions arguments
            // and setters list:
            if (!$this->container->hasDefinition($id = $this->container->getAlias($attribute->get()))) {
                throw new \InvalidArgumentException(sprintf('A definition with id "%s" does not exist.', $id));
            }

            // if the reference id matches the current resolving, a circular
            // reference occured:
            if ($current === $id || $self === $id) {
                throw new CircularReferenceException(
                    sprintf('Service \'%s\' has circular reference on \'%s\'', $id, $current === $id ? $self : $current)
                );
            }

            if ($self === $id && null !== $self) {
                continue;
            }

            $this->checkDefininition($this->container->getDefinition($id), $id, $current);
        }
    }

    /**
     * Extract Setter arguments and pass them to the reference check.
     *
     * @param array $setters
     * @param string $current
     * @param string $self
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
