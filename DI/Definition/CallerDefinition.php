<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Definition;

use \Selene\Components\DI\Reference;

/**
 * @class ReferenceCaller
 * @package Selene\Components\DI
 * @version $Id$
 */
class CallerDefinition
{
    /**
     * Constructor.
     *
     * @param string|Definition $service
     * @param string            $method
     * @param array             $arguments
     */
    public function __construct($service, $method, array $arguments = [])
    {
        $this->setService($service);

        $this->method    = $method;
        $this->arguments = $arguments;
    }

    /**
     * setSertvice
     *
     * @param mixed $service
     *
     * @return void
     */
    public function setService($service)
    {
        $this->service = $service instanceof Reference ? $service : new Reference($service);
    }

    /**
     * getService
     *
     *
     * @return Reference
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * setMethod
     *
     * @param string $method
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * getMethod
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * setArguments
     *
     * @param array $arguments
     *
     * @return void
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * getArguments
     *
     * @param array $arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * replaceArgument
     *
     * @param mixed $argument
     * @param int $index
     *
     * @throw \OutOfBoundsException if index exceed argument index
     * @return void
     */
    public function replaceArgument($argument, $index = 0)
    {
        if (0 > $index || count($this->arguments) < ($index + 1)) {
            throw new \OutOfBoundsException();
        }

        unset($this->arguments[$index]);
        $this->arguments[$index] = $argument;
    }
}
