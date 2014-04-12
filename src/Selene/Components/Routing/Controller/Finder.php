<?php

/**
 * This File is part of the Selene\Components\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class Finder
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
class Finder implements ResolverInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * find
     *
     * @param Route $route
     *
     * @throws \RuntimeException
     * @access public
     * @return callable
     */
    public function find($action, $method)
    {
        list ($controller, $action) = $this->findController($action, $method);

        if (method_exists($controller, $action)) {
            return [$controller, $action];
        }

        throw new \RuntimeException(
            sprintf('%s has no method %s', get_class($controller), $action)
        );
    }

    /**
     * getControllerAction
     *
     * @param mixed $controller
     * @param mixed $method
     *
     * @throws \RuntimeException
     * @access protected
     * @return array
     */
    protected function findController($controller, $method)
    {
        list ($controller, $action) = array_pad(explode(':', $controller), 2, null);

        if (null === $action) {
            $action = $this->getControllerAction($controller, $method, $path);
        }

        if ($this->container && $this->container->hasDefinition($controller)) {
            return [$this->container->get($controller), $action];
        }

        if (class_exists($controller)) {
            return [new $controller, $action];
        }

        throw new \RuntimeException('no controller found');
    }

    /**
     * getControllerAction
     *
     * @param mixed $controller
     * @param mixed $method
     *
     * @access protected
     * @return mixed
     */
    protected function getControllerAction($controller, $method, $path)
    {
        return 'handleMissingMethod';
    }

    /**
     * methodMap
     *
     * @param mixed $method
     *
     * @access protected
     * @return array
     */
    protected static function methodMap($method)
    {
        return [
            'get'    => 'index',
            'head'   => 'index',
            'put'    => 'update',
            'patch'  => 'update',
            'post'   => 'create',
            'delete' => 'delete',
        ];
    }
}
