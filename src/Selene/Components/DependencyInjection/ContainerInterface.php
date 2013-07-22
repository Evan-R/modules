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

use Closure;
use ArrayAccess;

/**
 * @class ContainerInterface
 * @package
 * @version $Id$
 */
interface ContainerInterface extends ArrayAccess
{
    /**
     * bind
     *
     * @param mixed $abstract
     * @param mixed $concrete
     * @access public
     * @return Container
     */
    public function bind($abstract, $concrete, $shared = false);

    /**
     * bind
     *
     * @param mixed $abstract
     * @param mixed $concrete
     * @access public
     * @return mixed
     */
    public function singleton($abstract, $concrete);

    /**
     * parameter
     *
     * @param mixed $parameter
     * @param mixed $value
     * @access public
     * @return mixed
     */
    public function parameter($parameter, $value);

    /**
     * share
     *
     * @param Closure $shared
     * @access public
     * @return mixed
     */
    public function share(Closure $shared);

    /**
     * resolve
     *
     * @param mixed $abstract
     * @param mixed $arguments
     * @access public
     * @return mixed
     */
    public function resolve($identifier, $arguments = []);

    /**
     * build
     *
     * @param mixed $implementation
     * @param mixed $arguments
     * @access public
     * @return mixed
     */
    public function build($implementation, $arguments = []);

    /**
     * call
     *
     * @param string $setter
     * @param string|array $parameters
     * @param string $binding
     * @access public
     */
    public function call($setter, $parameters = [], $binding = null);

    /**
     * arguments
     *
     * @param array $parameters
     * @param mixed $binding
     * @access public
     * @return mixed
     */
    public function addArguments(array $parameters = [], $binding = null);

    /**
     * addArgument
     *
     * @param array $parameters
     * @param mixed $binding
     * @access public
     * @return Container
     */
    public function addArgument($parameter, $binding = null);

    /**
     * extend
     *
     * @param mixed $binding
     * @param Closure $extends
     * @access public
     * @return void
     */
    public function extend($binding, Closure $extends);

    /**
     * instance
     *
     * @param mixed $binding
     * @param mixed $instance
     * @access public
     * @return mixed
     */
    public function instance($binding, $instance);
}
