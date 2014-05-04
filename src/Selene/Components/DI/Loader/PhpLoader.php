<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Config\Resource\Loader;
use \Selene\Components\Config\Resource\LocatorInterface;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PhpLoader extends Loader
{
    /**
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator)
    {
        parent::__construct($locator);
        $this->container = $builder->getContainer();
        $this->builder = $builder;
    }

    /**
     * Load a php file resource.
     *
     * @param mixed $resource
     *
     * @access public
     * @return void
     */
    public function load($resource)
    {
        $file = $this->locator->locate($resource, false);

        $builder = $this->builder;
        $container = $this->container;

        include $file;

        $builder->addFileResource($file);
    }

    /**
     * {@inheritdoc}
     * @param string $format
     */
    public function supports($resource)
    {
        return is_string($resource) && 'php' ===  pathinfo(strtolower($resource), PATHINFO_EXTENSION);
    }
}
