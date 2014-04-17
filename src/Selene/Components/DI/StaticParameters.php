<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

use \Selene\Components\Common\Traits\Getter;

/**
 * @class CompiledParameters
 * @package Selene\Components\DI
 * @version $Id$
 */
class StaticParameters extends Parameters
{
    use Getter;

    /**
     * __construct
     *
     * @param array $parameters
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $parameters)
    {
        $this->resolved = true;
        $this->parameters = $parameters;
    }

    /**
     * set
     *
     * @param mixed $param
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function set($param, $value)
    {
        $this->handleException('set');
    }

    /**
     * resolveParam
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function resolveParam($param)
    {
        $this->handleException('resolveValue');
    }

    /**
     * resolveValue
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function resolveValue($param)
    {
        $this->handleException('resolveValue');
    }

    /**
     * get
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function get($param)
    {
        return $this->getDefault($this->parameters, $param, null);
    }

    /**
     * has
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function has($param)
    {
        array_key_exists($param, $this->parameters());
    }

    /**
     *
     * @access public
     * @return mixed
     */
    public function all()
    {
        return $this->getParameters();
    }

    /**
     * getRaw
     *
     *
     * @access public
     * @return mixed
     */
    public function getRaw()
    {
        return $this->all();
    }

    /**
     * merge
     *
     * @param ParameterInterface $parameters
     *
     * @access public
     * @return mixed
     */
    public function merge(ParameterInterface $parameters)
    {
        if (!$parameters instanceof StaticParameters) {
            throw new \InvalidArgumentException(
                sprintf('%s can only be merged with as static parameter collection', __CLASS__)
            );
        }

        $this->replaceParameters(array_merge($this->getParameters(), $parameters->all()));
    }

    /**
     * getParameters
     *
     * @access protected
     * @return mixed
     */
    protected function &getParameters()
    {
        return $this->parameters;
    }

    /**
     * handleException
     *
     * @param mixed $method
     *
     * @access private
     * @return mixed
     */
    private function handleException($method)
    {
        throw new \Exception(sprintf('connot call %s() on a locked parameter collection', $method));
    }
}
