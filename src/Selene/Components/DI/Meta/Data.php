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

/**
 * @class Data
 * @package Selene\Components\DI\Meta
 * @version $Id$
 */
class Data implements MetaDataInterface
{
    use Getter;

    /**
     * arguments
     *
     * @var array
     */
    protected $parameters;

    /**
     * @param mixed $name
     * @param array $arguments
     *
     * @access public
     */
    public function __construct($name, array $parameters = [])
    {
        $this->setParameters($name, $parameters);
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->getDefault($this->parameters, 'name');
    }

    /**
     * get
     *
     * @param mixed $argument
     * @param mixed $default
     *
     * @access public
     * @return mixed
     */
    public function get($parameter, $default = null)
    {
        return $this->getDefault($this->parameters, $parameter, $default);
    }

    /**
     * @access public
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
     * @access protected
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
            throw new \InvalidArgumentException('no name given');
        }

        $this->parameters = $parameters;
    }
}
