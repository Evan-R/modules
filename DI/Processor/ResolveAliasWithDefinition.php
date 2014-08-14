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

use \Selene\Module\DI\Reference;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Definition\DefinitionInterface;

/**
 * @class ResolveAliasWithDefinition
 * @package Selene\Module\DI
 * @version $Id$
 */
class ResolveAliasWithDefinition implements ProcessInterface
{
    private $container;

    private $currentId;

    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        foreach ($container->getAliases() as $aliasId => $alias) {
            $id = $alias->getId();

            if (!$this->container->hasDefinition($id)) {
                throw new \InvalidArgumentException(sprintf('Container has no service with id "%s"', $id));
            }

            $this->replaceAlias($this->container->getDefinition($id), $id, $aliasId);
        }
    }

    /**
     * replaceAlias
     *
     * @param mixed $definition
     * @param mixed $id
     * @param mixed $aliasId
     *
     * @return void
     */
    private function replaceAlias($definition, $id, $aliasId)
    {
        foreach ($this->container->getDefinitions() as $defId => $definition) {

            if ($definition->hasArguments()) {
                $definition->setArguments($this->replaceArguments($definition->getArguments(), $id, $aliasId));
            }

            if ($definition->hasSetters()) {
                $setters = [];
                foreach ($definition->getSetters() as $i => $setter) {
                    $method = key($setter);
                    $setters[$i] = [$method => $this->replaceArguments($setter[$method], $id, $aliasId)];
                }

                $definition->setSetters($setters);
            }
        }
    }

    /**
     * replaceArguments
     *
     * @param array  $arguments
     * @param string $id
     * @param string $aliasId
     *
     * @return array
     */
    private function replaceArguments(array $arguments, $id, $aliasId)
    {
        foreach ($arguments as $i => &$argument) {

            if (is_scalar($argument)) {
                continue;
            }

            if (is_array($argument)) {
                $argument = $this->replaceArguments($argument, $id, $aliasId);
            } elseif ($argument instanceof Reference && $aliasId === (string)$argument) {
                $argument = new Reference($id);
            }
        }

        return $arguments;
    }
}
