<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Exception\ContainerResolveException;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\Common\SeparatorParserInterface;

/**
 * @class Resolver implements ResolverInterface, ContainerAwareInterface
 * @see ResolverInterface
 * @see ContainerAwareInterface
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Resolver implements ResolverInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * parser
     *
     * @var ResolverParser
     */
    protected $parser;

    /**
     * @param ContainerInterface $container
     * @param SeparatorParserInterface $parser
     *
     * @access public
     */
    public function __construct(ContainerInterface $container = null, SeparatorParserInterface $parser = null)
    {
        $this->container = $container;
        $this->parser    = $parser ?: new ResolverParser;
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
    public function setNamespaceAlias($alias, $namespace)
    {
        $this->parser->setNamespaceAlias($alias, $namespace);
    }

    /**
     * registerNamespaces
     *
     * @param array $namespaces
     *
     * @access public
     * @return void
     */
    public function setNamespaceAliases(array $namespaces)
    {
        foreach ($namespaces as $alias => $namespace) {
            $this->setNamespaceAlias($alias, $namespace);
        }
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

        if (class_exists($controller)) {
            $instance = new $controller;
        } elseif ($this->container) {

            try {
                $instance = $this->container->get($controller);
            } catch (\ContainerResolveException $e) {
                throw new \RuntimeException(sprintf('controller for id %s could not be resolved', $controller));
            } catch (\Exception $e) {
                throw $e;
            }

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
        if ($this->parser->supports($controller)) {
            list ($controllerClass, $action) = $this->parser->parse($controller);

            return [$controllerClass, $action];
        }

        if ($this->controllerIsService($controller)) {
            return explode(':', $controller);
        }

        return array_pad(explode('@', $controller), 2, null);
    }

    /**
     * controllerIsService
     *
     * @param mixed $controller
     *
     * @access protected
     * @return boolean
     */
    protected function controllerIsService($controller)
    {
        return 1 === substr_count($controller, ':');
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
