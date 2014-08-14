<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Loader;

use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Config\Loader\PhpFileLoader;
use \Selene\Module\Config\Resource\LocatorInterface;
use \Selene\Module\Config\Traits\ContainerBuilderAwareLoaderTrait;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Module\DI
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
