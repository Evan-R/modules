<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\Config\Traits\CallableLoaderHelperTrait;

/**
 * @class CallableLoader extends RoutingLoader
 * @see RoutingLoader
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CallableLoader extends RoutingLoader
{
    use CallableLoaderHelperTrait;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $any = false)
    {
        call_user_func($resource, $this->routes);

        $this->builder->addFileResource($this->findResourceOrigin($resource));
        $this->prepareContainer();
    }

    /**
     * doLoad
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function doLoad($file)
    {
        return;
    }
}
