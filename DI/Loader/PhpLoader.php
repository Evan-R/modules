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
use \Selene\Components\Config\Loader\PhpFileLoader;
use \Selene\Components\Config\Resource\LocatorInterface;
use \Selene\Components\Config\Traits\ContainerBuilderAwareLoaderTrait;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PhpLoader extends PhpFileLoader
{
    use ContainerBuilderAwareLoaderTrait;

    /**
     * Constructor.
     *
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator)
    {
        parent::__construct($locator);

        $this->container = $builder->getContainer();
        $this->setBuilder($builder);
    }

    /**
     * {@inheritdoc}
     */
    protected function doLoad($file)
    {
        $builder   = $this->builder;
        $container = $this->container;

        include $file;
    }
}
