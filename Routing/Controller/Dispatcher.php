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

use \Selene\Components\Routing\Matchers\MatchContext;
use \Selene\Components\Routing\Mapper\ParameterMapper;

/**
 * @class Dispatcher
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
class Dispatcher
{
    private $mapper;

    public function __construct(ResolverInterface $resolver, ParameterMapper $mapper = null)
    {
        $this->resolver = $resolver;
        $this->mapper   = $mapper ?: new ParameterMapper;
    }

    public function dispatch(MatchContext $context)
    {

        if (!$action = $this->resolver->find($context->getRoute()->getAction(), $context->getRequest()->getMethod())) {
            return false;
        }

        list ($controller, $method) = $action;

        if (false === ($arguments = $this->mapper->map(
            $context->getRequest(),
            $context->getParameters(),
            new \ReflectionObject($controller),
            $method
        ))) {
            return false;
        }

        return call_user_func_array($action, $arguments);
    }
}
