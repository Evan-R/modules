<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

use \Selene\Module\DI\Reference;

/**
 * @class ReferenceCaller
 * @package Selene\Module\DI
 * @version $Id$
 */
class CallerReference
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
     * fromString
     *
     * @param string $reference
     *
     * @return CallerReference
     */
    public static function fromString($reference)
    {
        if (!static::isReferenceString($reference)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a suitable reference string.'));
        }

        list ($service, $method) = explode('->', substr($reference, strlen(ContainerInterface::SERVICE_REF_INDICATOR)));

        if (0 === substr_count($method, '(')) {
            return new self($service, $method);
        }

        $arguments = [];

        if (preg_match('~\((.*)\)~', $method, $matches)) {
            if (isset($matches[1])) {
                $arguments = preg_split('~,\s+?~', $matches[1], -1, PREG_SPLIT_NO_EMPTY);
            }
        }

        $method = substr($method, 0, strpos($method, '('));

        return new self($service, $method, $arguments);
    }

    /**
     * isReferenceString
     *
     * @param string $string
     *
     * @return boolean
     */
    public static function isReferenceString($string)
    {
        return 0 === strpos($string, ContainerInterface::SERVICE_REF_INDICATOR)
            && 1 === substr_count($string, '->');
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
