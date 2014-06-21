<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Mapper;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @class ParamterMapper
 * @package Selene\Components\Routing
 * @version $Id$
 */
class ParameterMapper
{
    private $pool;

    private $request;

    public function __construct()
    {
        $this->pool = [];
    }

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

    private function getRequestReflection(Request $request)
    {
        return $this->request ? $this->request : $this->request = new \ReflectionObject($request);
    }

    private function getReflectionMethod(\ReflectionObject $ref, $method)
    {
        $name = $ref->getName();
        if (isset($this->pool[$name][$method])) {
            return $this->pool[$name][$method];
        }

        if ($ref->hasMethod($method) && (($rm = $ref->getMethod($method)) && $rm->isPublic())) {
            return $this->pool[$name][$method] = $rm;
        }

        return false;
    }
}
