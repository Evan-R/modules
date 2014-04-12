<?php

/**
 * This File is part of the Selene\Components\Routing\Config\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Config\Stubs;

/**
 * @class PhpLoader
 * @package Selene\Components\Routing\Config\Stubs
 * @version $Id$
 */
class PhpLoader
{
    /**
     * routeBuilder
     *
     * @var mixed
     */
    private $routeBuilder;

    /**
     * load
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function load($file)
    {
        $builder = $this->routeBuilder;

        require $file;

        return $builder->getRoutes();
    }

    /**
     * supports
     *
     *
     * @access public
     * @return mixed
     */
    public function supports()
    {
        return ['php'];
    }

    /**
     * getRouteBuilder
     *
     * @access protected
     * @return mixed
     */
    protected function getRouteBuilder()
    {
        $this->routeBuilder;
    }
}
