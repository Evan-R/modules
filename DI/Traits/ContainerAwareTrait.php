<?php

/**
 * This File is part of the Selene\Components\DI\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Traits;

use \Selene\Components\DI\ContainerInterface;

/**
 * @trait ContainerAware
 *
 * @package Selene\Components\DI\Traits
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
}