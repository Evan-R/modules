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
use \Selene\Components\Routing\Mapper\ParameterMapper;
use \Selene\Components\Routing\Matchers\MatchContext;

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
class Dispatcher implements DispatcherInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * parser
     *
     * @var ResolverParser
     */
    protected $parser;

    /**
     * mapper
     *
     * @var ParameterMapper
     */
    protected $mapper;

    /**
     * mapArguments
     *
     * @var boolean
     */
    protected $mapParameters;

    /**
     * Constructor.
     *
     * @param ParameterMapper $mapper
     * @param SeparatorParserInterface $parser
     *
     * @access public
     * @return mixed
     */
    public function __construct(SeparatorParserInterface $parser = null, ParameterMapper $mapper = null)
    {
        $this->mapper = $mapper ?: new ParameterMapper;
        $this->parser = $parser ?: new Parser;

        $this->mapParameters = false;
    }

    /**
     * Advices the resolver to map parameters from the matching context to the
     * controller arguments.
     *
     * @param boolean $map
     *
     * @return void
     */
    public function mapParameters($map)
    {
        $this->mapParameters = (bool)$map;
    }

    /**
     * Resolves and dispatches the controller by a match context.
     *
     * @param string $action
     * @param string $method http verb
     *
     * @throws \RuntimeException if no controller was found.
     * @throws \RuntimeException if parameter mapping fails.
     *
     * @return mixed the result of the controller action.
     */
    public function dispatch(MatchContext $context)
    {
        list ($controller, $action, $callAction) = $this->findController($context);

        if ($this->mapParameters) {
            if (null === ($arguments = $this->getParameters($context, $controller, $action))) {
                throw new \RuntimeException(
                    sprintf('Arguments mismatch for controller %s::$s()', get_class($controller), $action)
                );
            }

        } else {
            $arguments = $context->getParameters();
        }

        // If callAction is present, wrap arguments in another array.
        // This will call callAction with a methof name of the actual
        // controller and its arguments.
        if (null !== $callAction) {
            $arguments = [$action, $arguments];
            $action = $callAction;
        }

        return call_user_func_array([$controller, $action], $arguments);
    }

    /**
     * mapParameters
     *
     * @param MatchContext $context
     * @param mixed $controller
     * @param mixed $action
     *
     * @return array
     */
    protected function getParameters(MatchContext $context, $controller, $method)
    {
        if (false === ($arguments = $this->mapper->map(
            $context->getRequest(),
            $context->getParameters(),
            new \ReflectionObject($controller),
            $method
        ))) {
            return;
        }

        return $arguments;
    }

    /**
     * getControllerAction
     *
     * @param mixed $controller
     * @param mixed $method
     *
     * @throws \RuntimeException controller cannot be resolved.
     *
     * @return callable an array of the controller instance an its method to be
     * called.
     */
    protected function findController(MatchContext $context)
    {
        $action  = $context->getRoute()->getAction();
        $method = $context->getRequest()->getMethod();

        list ($controller, $action) = $this->extractControllerAction($action);

        if (null === $action) {
            $action = $this->getControllerAction($controller, $method);
        }

        $instance = null;

        if (class_exists($controller)) {
            $instance = new $controller;
        } elseif (null !== $this->getContainer()) {

            try {
                $instance = $this->container->get($controller);
            } catch (\ContainerResolveException $e) {
                throw new \RuntimeException(sprintf('controller for id %s could not be resolved', $controller));
            } catch (\Exception $e) {
                throw $e;
            }

        } else {
            throw new \RuntimeException(sprintf('Controller "%s" could not be found.', $controller));
        }

        if ($this->container && $instance instanceof ContainerAwareInterface) {
            $instance->setContainer($this->container);
        }

        if (!method_exists($controller, $action)) {
            throw new \RuntimeException(
                sprintf('%s has no method %s', get_class($controller), $action)
            );
        }

        return [$instance, $action, $instance instanceof Controller ? 'callAction' : null];
    }

    /**
     * extractControllerAction
     *
     * @param mixed $controller
     *
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
     * @return mixed
     */
    protected function getControllerAction($controller, $method)
    {
        return 'handleMissingMethod';
    }
}
