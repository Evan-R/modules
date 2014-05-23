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
class ResolveDefinitionDependencies implements ProcessInterface
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
        $parameters = $container->getParameters();

        foreach ($container->getDefinitions() as $id => $definition) {

            $class = $parameters->resolveParam($definition->getClass());

            if (0 < strlen($class) && !class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('class "%s" does not exist', $class));
            }

            $definition->setClass($class);

            if ($definition->requiresFile()) {

                if (!is_file($file = $parameters->resolveParam($definition->getFile()))) {
                    throw new \InvalidArgumentException(sprintf('file "%s" does not exist', $file));
                }

                $definition->setFile($file);
            }
        }
    }
}
