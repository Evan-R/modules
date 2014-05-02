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
use \Selene\Components\DI\Exception\CircularReferenceException;

/**
 * @class ResolveCircularReference implements ProcessInterface
 * @see ProcessInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ResolveCircularReference implements ProcessInterface
{
    private $container;

    private $resolving;

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


        foreach ($services = $container->getDefinitions() as $id => $service) {
            $this->resolving = $id;
            $this->checkCircularReference($service->getArguments(), $services, $id);

            foreach ((array)$service->getSetters() as $setter) {
                $this->checkCircularReference(array_values($setter), $services, $id);
            }
        }

        $this->resolving = null;
    }

    /**
     * checkCircularReference
     *
     * @param array $attributes
     * @param array $services
     * @param mixed $current
     *
     * @access protected
     * @return mixed
     */
    protected function checkCircularReference(array $attributes, array $services, $current)
    {
        foreach ($attributes as $attribute) {
            if ($this->container->isReference($attribute)) {

                if (isset($services[$current])) {

                    $id = null;
                    $service = $services[$current];

                    if ($this->resolving === $id) {
                        throw new CircularReferenceException(
                            sprintf('service \'%s\' has circular reference \'%s\'', $current, $id)
                        );
                    }

                    if ($service->hasArguments()) {
                        $this->checkCircularReference($service->getArguments(), $services, $id);
                    }

                    if ($service->hasSetters()) {
                        $this->checkCircularReference($service->getSetters(), $services, $id);
                    }
                }
            }
        }
    }

    /**
     * isProtorype
     *
     * @param Definition $service
     *
     * @access protected
     * @return mixed
     */
    protected function isProtorype(Definition $service)
    {
        return ContainerInterface::SCOPE_PROTOTYPE === $service->getScope();
    }
}
