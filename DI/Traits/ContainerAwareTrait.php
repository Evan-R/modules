<?php

/**
 * This File is part of the Selene\Module\DI\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Traits;

use \Selene\Module\DI\ContainerInterface;

/**
 * @trait ContainerAware
 *
 * @package Selene\Module\DI\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait ContainerAwareTrait
{
    /**
     * container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * setContainer
     *
     * @param ContainerInterface $container A DI container instance.
     *
     * @access public
     * @return mixed
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * getContainer
     *
     *
     * @access public
     * @return ContainerInteface An instance of a DI Container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * hasService
     *
     * @param string $id
     *
     * @return boolean
     */
    protected function hasService($id)
    {
        if (null !== $this->container) {
            return $this->container->has($id);
        }

        return false;
    }

    /**
     * getService
     *
     * @param mixed $id
     *
     * @return object
     */
    protected function getService($id)
    {
        if (null === $this->container) {
            throw new \BadMethodCallException('No container set.');
        }

        return $this->container->get($id);
    }
}
