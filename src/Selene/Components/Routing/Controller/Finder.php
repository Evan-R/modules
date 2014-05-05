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
     * packages
     *
     * @var array
     */
    protected $namespaces;

    /**
     * @param ContainerInterface $container
     *
     * @access public
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
     * registerPackages
     *
     * @param array $packages
     *
     * @access public
     * @return void
     */
    public function registerNamespace($alias, $namespace)
    {
        $this->namespaces[$alias] = $namespace;
    }

    /**
     * registerNamespaces
     *
     * @param array $namespaces
     *
     * @access public
     * @return void
     */
    public function registerNamespaces(array $namespaces)
    {
        foreach ($namespaces as $alias => $namespaces) {
            $this->registerNamesapce($alias, $namespace);
        }
    }

    /**
     * hasPackages
     *
     * @access protected
     * @return boolean
     */
    protected function hasControllerNamespaces()
    {
        return null !== $this->namespaces;
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
        list ($controller, $action) = $this->extractControllerAction($controller);

        if (null === $action) {
            $action = $this->getControllerAction($controller, $method);
        }

        $instance = null;

        if ($this->container && $this->container->hasDefinition($controller)) {
            $instance = $this->container->get($controller);
        } elseif (class_exists($controller)) {
            $instance = new $controller;
        } else {
            throw new \RuntimeException('no controller found');
        }

        if ($this->container && $instance instanceof ContainerAwareInterface) {
            $instance->setContainer($this->container);
        }
        return [$instance, $action];
    }

    /**
     * extractControllerAction
     *
     * @param mixed $controller
     *
     * @access protected
     * @return array
     */
    protected function extractControllerAction($controller)
    {
        if ($this->hasControllerNamespaces() && false !== ($pos = strpos($controller, ':')) &&
            false !== ($rpos = strrpos($controller, ':')) && $pos !== $rpos
        ) {
            $parts = explode(':', $controller);

            if (!isset($this->namespaces[$parts[0]])) {
                throw new \InvalidArgumentException(sprintf('no namespace set for alias %s', $parts[0]));
            }

            $ns = $this->namespaces[$parts[0]];

            $controllerClass = $ns . '\\Controller\\' . ucfirst(strtr($parts[1], ['.' => '\\'])).'Controller';
            $action = $parts[2] . 'Action';

            return [$controllerClass, $action];
        }

        return array_pad(explode('@', $controller), 2, null);
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
    protected function getControllerAction($controller, $method)
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
