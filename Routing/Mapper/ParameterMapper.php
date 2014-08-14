<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Mapper;

use \Symfony\Component\HttpFoundation\Request;

/**
 * Maps Request parameters to Controller arguments.
 *
 * @class ParamterMapper
 * @package Selene\Module\Routing
 * @version $Id$
 */
class ParameterMapper
{
    /**
     * pool
     *
     * @var array
     */
    private $pool;

    /**
     * request
     *
     * @var Request
     */
    private $request;

    /**
     * Creates a new ParameterMapper
     */
    public function __construct()
    {
        $this->pool = [];
    }

    /**
     * map
     *
     * @param Request $request
     * @param array $input
     * @param \ReflectionObject $ref
     * @param string $method
     *
     * @return array
     */
    public function map(Request $request, array $input, \ReflectionObject $ref, $method)
    {
        if (!$rm = $this->getReflectionMethod($ref, $method)) {
            return false;
        }

        $args = [];

        foreach ($rm->getParameters() as $argument) {
            $name = $argument->getName();

            if (array_key_exists($name, $input)) {
                $args[$name] = $input[$name];
            } elseif ($this->parameterIsRequest($request, $argument)) {
                $args[$name] = $request;
            } else {
                return false;
            }
        }

        return $args;
    }

    /**
     * parameterIsRequest
     *
     * @param Request $request
     * @param \ReflectionParameter $param
     *
     * @return boolean
     */
    private function parameterIsRequest(Request $request, \ReflectionParameter $param)
    {
        try {
            if (!$class = $param->getClass()) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return 0 === strcmp(get_class($request), $class->getName());
    }

    /**
     * getReflectionMethod
     *
     * @param \ReflectionObject $ref
     * @param string $method
     *
     * @return \ReflectionMethod
     */
    private function getReflectionMethod(\ReflectionObject $ref, $method)
    {
        $name = $ref->getName();
        if (isset($this->pool[$name][$method])) {
            return $this->pool[$name][$method];
        }

        if ($ref->hasMethod($method) && (($rm = $ref->getMethod($method)) && $rm->isPublic())) {
            return $this->pool[$name][$method] = $rm;
        }
    }
}
