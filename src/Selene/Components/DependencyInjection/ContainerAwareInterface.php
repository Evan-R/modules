<?php

/**
 * This File is part of the Selene\Components\DependencyInjection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection;

/**
 * @interface ContainerAwareInterface ContainerAwareInterface
 *
 * @package Selene\Components\DependencyInjection
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
}
