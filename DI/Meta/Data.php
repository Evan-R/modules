<?php

/**
 * This File is part of the Selene\Components\DI\Meta package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Meta;

use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\Common\Helper\ListHelper;

/**
 * @class Data
 * @package Selene\Components\DI\Meta
 * @version $Id$
 */
class Data implements MetaDataInterface
{
    use Getter;

    protected $name;

    /**
     * arguments
     *
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array $arguments
     */
    public function __construct($name, array $parameters = [])
    {
        $this->setParameters($name, $parameters);
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get
     *
     * @param mixed $argument
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($parameter, $default = null)
    {
        if (!is_string($parameter)) {
            return;
        }

        if (!ListHelper::arrayIsList($this->parameters)) {
            return $this->getDefault($this->parameters, $parameter, $default);
        }

        $params = [];

        foreach ($this->parameters as $param) {
            if (!$value = $this->getDefault((array)$param, $parameter, null)) {
                continue;
            }
            $params[] = $value;
        }

        return (bool)$params ? $params : null;
    }

    /**
     * getParameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * setArguments
     *
     * @param mixed $name
     * @param array $arguments
     *
     * @return void
     */
    protected function setParameters($name, array $parameters)
    {
        if (is_array($name)) {
            $parameters = array_merge($parameters, $name);
        } else {
            $parameters['name'] = $name;
        }

        if (!isset($parameters['name'])) {
            throw new \InvalidArgumentException(sprintf('%s: No name given', get_class($this)));
        }

        $this->name = $parameters['name'];

        unset($parameters['name']);

        $this->parameters = $parameters;
    }
}
