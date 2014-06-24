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
use \Selene\Components\Config\Traits\CallableLoaderHelperTrait;

/**
 * @class CallableLoader extends Loader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CallableLoader extends Loader
{
    use CallableLoaderHelperTrait;

    /**
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator)
    {
        $this->container = $builder->getContainer();
        $this->builder = $builder;

        parent::__construct($locator);
    }

    /**
     * Load a callable entity resuorce.
     *
     * @param callable $resource
     *
     * @access public
     * @return void
     */
    public function load($callable, $any = false)
    {
        $resource = $this->findResourceOrigin($callable);

        call_user_func($callable, $this->builder);
        $this->builder->addFileResource($resource);
    }

    protected function doLoad($file)
    {
    }
}
