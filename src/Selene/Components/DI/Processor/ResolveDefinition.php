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

/**
 * @class ResolveDefinitionPass
 * @package Selene\Components\DI\Resolver\Pass
 * @version $Id$
 */
class ResolveDefinition implements ProcessInterface
{

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
        $this->resolveDefinitions($container);
    }

    /**
     * resolveDefinitions
     *
     * @param mixed $container
     *
     * @access protected
     * @return mixed
     */
    protected function resolveDefinitions($container)
    {
        $parameters = $container->getParameters();

        foreach ($container->getDefinitions() as $id => $definition) {

            if ($definition->requiresFile()) {

            }

            $class = $parameters->resolveParam($definition->getClass());

            if (0 < strlen($class) && !class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('class "%s" does not exist', $class));
            }

            $definition->setClass($class);

            if ($definition->hasArguments()) {
                $definition->setArguments($parameters->resolveParam($definition->getArguments()));
            }

            if ($definition->hasFactory()) {
                $args = $definition->getArguments();
                array_unshift($args, $definition->getClass());
                $definition->setArguments($args);
            }

            if ($definition->hasSetters()) {
                $definition->setSetters($parameters->resolveParam($definition->getSetters()));
            }
        }
    }
}
