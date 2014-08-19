<?php

/*
 * This File is part of the Selene\Module\Config\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Loader;

use \Selene\Module\Config\Traits\CallableLoaderHelperTrait;

/**
 * @class CallableLoader
 * @package Selene\Module\Config\Loader
 * @version $Id$
 */
abstract class CallableLoader extends AbstractLoader
{
    use CallableLoaderHelperTrait;

    /**
     * Execute a callable resource.
     *
     * @param callable $resource
     * @param boolean $any
     *
     * @return void
     */
    public function load($resource, $any = self::LOAD_ONE)
    {
        $this->doLoad($resource);
        $this->notify($this->findResourceOrigin($resource));
    }

    /**
     * doLoad
     *
     * @param callable $resource
     *
     * @return void
     */
    protected function doLoad($resource)
    {
        call_user_func($resource);
    }

    /**
     * findResourceOrigin
     *
     * @return string|array
     */
    protected function findResource($resource, $any = self::LOAD_ONE)
    {
        return $resource;
    }
}
