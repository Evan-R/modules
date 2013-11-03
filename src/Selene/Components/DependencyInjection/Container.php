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

/**
 * @class Container implements InspectableInterface Container
 * @see InspectableInterface
 *
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Container implements InspectableInterface
{
    const SCOPE_CONTAINER = 'container';

    const SCOPE_PROTOTYPE = 'prototype';

    /**
     * parameters
     *
     * @var Parameters
     */
    protected $parameters;

    /**
     * services
     *
     * @var mixed
     */
    protected $services;

    /**
     *
     * @param Parameters $parameters
     *
     * @access public
     */
    public function __construct(Parameters $parameters = null)
    {
        $this->setParameter($parameters);
    }

    /**
     * setParameter
     *
     * @param Parameters $parameters
     *
     * @access protected
     * @return void
     */
    protected function setParameter(Parameters $parameters = null)
    {
        if (null === $parameters) {
            $parameters = new Parameters;
        }
        $this->parameters = $parameters;
    }

    /**
     * setParam
     *
     * @param mixed $param
     * @param mixed $definition
     *
     * @access public
     * @return mixed
     */
    public function setParam($param, $definition)
    {
        $this->parameters->set($param, $definition);
    }

    /**
     * getParam
     *
     * @param mixed $param
     * @param mixed $definition
     *
     * @access public
     * @return mixed
     */
    public function getParam($param)
    {
        $this->params->get($param);
    }

    /**
     * parameters
     *
     * @access public
     * @return Parameters
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /**
     * setService
     *
     * @param string $service
     * @param string $class
     *
     * @access public
     * @return Definition
     */
    public function setService($service, $class, $arguments = null)
    {
        $this->services[$this->parameters->get($service)] =
            $definition = new Definition($this->parameters->get($class), $arguments);

        return $definition;
    }

    /**
     * getService
     *
     * @param mixed $service
     *
     * @access public
     * @return mixed
     */
    public function getService($service)
    {
        if ($this->hasService($service)) {
            return $this->resolveService($service);
        }
        throw new \Exception(sprintf('service %s not found', $service));
    }

    protected function isReference($reference)
    {
        return 0 === strrpos($reference, '$') && $this->hasService(substr($reference, 1));
    }

    protected function resolveService($service)
    {

        $definition = $this->services[$service];

        if ($definition->isResolved()) {
            return $definition->getResolved();
        }

        $params = $this->resolveServiceArgs($definition);
        $instance;

        if (count($params) > 0) {
            $instance = $this->setClassArgs($definition->getClass(), $params);
        } else {
            $class = $definition->getClass();
            $instance = new $class;
        }

        $definition->setResolved($instance);
        return $instance;
    }

    /**
     * hasService
     *
     * @param mixed $service
     *
     * @access public
     * @return mixed
     */
    public function hasService($service)
    {
        return is_string($service) and isset($this->services[$service]);
    }

    /**
     * getDefinitionArguments
     *
     * @param Definition $definition
     *
     * @access protected
     * @return mixed
     */
    protected function getDefinitionArguments(Definition $definition)
    {
        $args = $definition->getArguments();

        if ($parentClass = $definition->getParent()) {
            if ($parameters = $this->parameters->get('@'.$parentClass)) {
                $args = array_unique(array_merge($parameters, $args));
            }
        }

        return $args;

    }

    protected function resolveServiceArgs(Definition $definition)
    {
        $args = $this->getDefinitionArguments($definition);
        $arguments = [];

        if (null !== $args) {
            foreach ($args as $argument) {
                if ($this->isReference($argument)) {
                    $arguments[] = $this->getService($this->getNameFromReference($argument));
                    continue;
                }
                $arguments[] = $this->parameters->get($argument);
            }
        }
        return $arguments;
    }

    protected function getNameFromReference($reference)
    {
        return substr($reference, 1);
    }

    protected function setClassArgs($class, $args)
    {
        $instance;

        switch(count($args)) {
            case 1:
                $instance = new $class($args[0]);
                break;
            case 2:
                $instance = new $class($args[0], $args[1]);
                break;
            case 3:
                $instance = new $class($args[0], $args[1], $args[2]);
                break;
            case 4:
                $instance = new $class($args[0], $args[1], $args[2], $args[3]);
                break;
            case 5:
                $instance = new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
                break;
            case 6:
                $instance = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                break;
            case 7:
                $instance = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
                break;
            default:
                throw new \InvalidArgumentException('no arguments or argument limit exceeded');
                break;
        }

        return $instance;
    }
}
