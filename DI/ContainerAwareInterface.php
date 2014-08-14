<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

/**
 * @interface ContainerAwareInterface ContainerAwareInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ContainerAwareInterface
{
    /**
     * setContainer
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function setContainer(ContainerInterface $container = null);

    /**
     * getContainer
     *
     *
     * @access public
     * @return ContainerInteface An instance of a DI Container.
     */
    public function getContainer();
}
