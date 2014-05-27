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
use \Selene\Components\DI\ParameterInterface;
use \Selene\Components\DI\Definition\DefinitionInterface;

/**
 * @class ResolveDefinitionDependencies implements ProcessInterface
 * @see ProcessInterface
 *
 * @package Selene\Components\DI
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
     * @access public
     * @return void
     */
    public function process(ContainerInterface $container)
    {
        $parameters = $container->getParameters();

        foreach ($container->getDefinitions() as $id => $definition) {
            $this->checkClassIntegrity($definition, $parameters);
            $this->checkFileIntegrity($definition, $parameters);
        }
    }

    /**
     * Set the resolve class string on the service definition.
     *
     * @param DefinitionInterface $definition
     *
     * @throws \InvalidArgumentException if the class does not exist.
     * @access private
     * @return void
     */
    private function checkClassIntegrity(DefinitionInterface $definition, ParameterInterface $parameters)
    {

        $class = $parameters->resolveParam($definition->getClass());

        if (!$definition->requiresFile() && (0 < strlen($class) && !class_exists($class))) {
            throw new \InvalidArgumentException(sprintf('class "%s" does not exist', $class));
        }

        $definition->setClass($class);
    }

    /**
     * Set the resolve file string on the service definition.
     *
     * @param DefinitionInterface $definition
     *
     * @throws \InvalidArgumentException if the file does not exist
     * @access private
     * @return void
     */
    private function checkFileIntegrity(DefinitionInterface $definition, ParameterInterface $parameters)
    {
        if (!$definition->requiresFile()) {
            return;
        }

        if (!is_file($file = $parameters->resolveParam($definition->getFile()))) {
            throw new \InvalidArgumentException(sprintf('file "%s" does not exist', $file));
        }

        $definition->setFile($file);
    }
}
