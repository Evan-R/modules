<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Controller;

use \Selene\Module\Common\SeparatorParserInterface;
use \Selene\Module\Routing\Mapper\ParameterMapper;
use \Selene\Module\Routing\Matchers\MatchContext;
use \Selene\Module\Routing\Event\RouteDispatched;

/**
 * @class Resolver implements ResolverInterface
 * @see ResolverInterface
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Dispatcher implements DispatcherInterface
{
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

    protected $cached;

    /**
     * Constructor.
     *
     * @param ParameterMapper $mapper
     * @param SeparatorParserInterface $parser
     */
    public function __construct(SeparatorParserInterface $parser = null, ParameterMapper $mapper = null)
    {
        $this->mapper = $mapper ?: new ParameterMapper;
        $this->parser = $parser ?: new Parser;

        $this->mapParameters = false;
        $this->cached = [];
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
    public function dispatch(MatchContext $context, RouteDispatched $event = null)
    {

        list ($controller, $action, $callAction) = $this->findController($context);


        // if theres a mapper and routing arguments mismatch controller
        // arguments, then throw an exception:
        if ($this->mapParameters) {
            if (null === ($arguments = $this->getParameters($context, $controller, $action))) {
                throw new \RuntimeException(
                    sprintf('Arguments mismatch for controller %s::%s()', get_class($controller), $action)
                );
            }
        } else {
            $arguments = $context->getParameters();
        }

        // If `callAction` is present (in case the controller inherits from
        // \Selene\Module\Routing\Controller\Controller), put the actual
        // method and arguments into an array that matches the `callAction`
        // call.
        if (null !== $callAction) {
            $arguments = [$action, $arguments];
            $action = $callAction;
        }

        // set the routing event on the controller
        if ($controller instanceof EventAware && null !== $event) {
            $controller->setEvent($event);
        }

        //$start = microtime(true);

        $res = call_user_func_array([$controller, $action], $arguments);
        //$end = microtime(true);
        //var_dump(($end - $start) * 1000);
        return $res;
    }

    /**
     * Get Parameters that match the controllers arguments.
     *
     * @param MatchContext $context
     * @param string $controller
     * @param string $action
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
     * Finds the controller.
     *
     * @param MatchContext $context
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

        //@TODO: do we really deed this?
        if (null === $action) {
            $action = $this->getControllerAction();
        }

        $instance = null;

        if (class_exists($controller)) {
            $instance = new $controller;
        } elseif ($this->hasService($controller)) {

            try {
                $instance = $this->getService($controller);
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf('controller for id %s could not be resolved', $controller));
            }

        } else {
            throw new \RuntimeException(sprintf('Controller "%s" could not be found.', $controller));
        }

        if (!method_exists($instance, $action)) {
            throw new \RuntimeException(
                sprintf('%s has no method %s', get_class($instance), $action)
            );
        }

        return [$instance, $action, $instance instanceof Controller ? 'callAction' : null];
    }

    /**
     * hasService
     *
     * @param string $id
     *
     * @return boolean
     */
    protected function hasService($id)
    {
        return false;
    }

    /**
     * getService
     *
     * @param string $id
     *
     * @return Object
     */
    protected function getService($id)
    {
        return null;
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
     * @return string
     */
    protected function getControllerAction()
    {
        return 'handleMissingMethod';
    }
}
