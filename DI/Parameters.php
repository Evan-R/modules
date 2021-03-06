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

use \Selene\Module\Common\Helper\StringHelper;
use \Selene\Module\DI\Exception\ParameterNotFoundException;
use \Selene\Module\DI\Exception\ParameterResolvingException;

/**
 * @class Parameters implements ParameterInterface Parameters
 * @see ParameterInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Parameters implements ParameterInterface
{
    /**
     * parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * resolved
     *
     * @var boolean
     */
    protected $resolved;

    /**
     * resolving
     *
     * @var array
     */
    private $resolving;

    /**
     * resolvedParams
     *
     * @var array
     */
    private $resolvedParams;

    public static $rightDelim = '%';

    public static $leftDelim  = '%';

    /**
     * Initialize parameter collection with data.
     */
    public function __construct(array $params = [])
    {
        $this->resolving = [];
        $this->replaceParams($params);
    }

    /**
     * Replaces all parameters.
     *
     * @param array $params
     *
     * @api
     *
     * @return void
     */
    public function replaceParams(array $params)
    {
        $this->setUnresolved();
        $this->parameters = array_change_key_case($params, CASE_LOWER);
    }

    /**
     * Sets a parameter.
     *
     * @param mixed $param
     * @param mixed $value
     *
     * @api
     *
     * @return void
     */
    public function set($param, $value)
    {
        $this->setUnresolved();
        $this->parameters[strtolower($param)] = $value;
    }

    /**
     * get
     *
     * @param mixed $param
     *
     * @api
     *
     * @throws \Selene\Module\DI\Exception\ParameterNotFoundException
     * @return mixed
     */
    public function get($param)
    {
        if ($this->has($param)) {
            $params = $this->getParameters();
            return $params[strtolower($param)];
        }

        throw new ParameterNotFoundException(
            sprintf('Parameter \'%s\' was not found', $param)
        );
    }

    /**
     * getRaw
     * @internal
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->parameters;
    }

    /**
     * Merges two objects that implement `\Selene\Module\DI\ParameterInterface`.
     *
     * Merging two objects will cause the current one to be reset to unresolved;
     *
     * @param ParameterInterface $parameters the collection to merge with.
     *
     * @api
     *
     * @throws \LogicException And exception is thrown if you try to merge this instance with itself.
     * @return void
     */
    public function merge(ParameterInterface $parameters)
    {
        if ($this === $parameters) {
            throw new \LogicException(sprintf('%s: cannot merge same instance', get_class($this)));
        }

        $this->parameters = array_merge($this->parameters, $parameters->getRaw());

        if ($parameters->isResolved() && $this->isResolved()) {
            $this->resolvedParams = array_merge($this->resolvedParams, $parameters->all());
        } else {
            $this->setUnresolved();
        }
    }

    /**
     * Check if a parameter exists.
     *
     * @param mixed $param
     *
     * @api
     *
     * @return boolean
     */
    public function has($param)
    {
        return array_key_exists(strtolower($param), $this->getParameters());
    }

    /**
     * Removes a parameter.
     *
     * @param mixed $param
     *
     * @api
     *
     * @return void
     */
    public function remove($param)
    {
        unset($this->parameters[$key = strtolower($param)]);
        unset($this->resolvedParams[$key]);
    }

    /**
     * Returns parameters depenging on its resolved state.
     *
     * @api
     *
     * @return array
     */
    public function all()
    {
        if ($this->resolved) {
            return $this->resolvedParams;

        }
        return $this->parameters;
    }

    /**
     * Resolves all parameters.
     *
     * @param mixed $parameters
     *
     * @api
     *
     * @return void
     */
    public function resolve()
    {
        if (!$this->isResolved()) {
            $this->doResolve();
        }

        return $this;
    }

    /**
     * Checks if the collection is resolved.
     *
     * @return boolean
     */
    public function isResolved()
    {
        return $this->resolved;
    }

    /**
     * Resolves a parameter or value with the given parameters `$this->parameters`.
     *
     * @param mixed $param
     *
     * @api
     *
     * @return mixed the resolved parameter or key (string, array, etc)
     */
    public function resolveParam($param)
    {
        if (is_array($param)) {

            $resolved = [];

            foreach ($param as $key => $value) {

                if (is_string($key)) {
                    $key = $this->resolveString($key);
                }

                $resolved[$key] = $this->resolveParam($value);
            }

            return $resolved;
        }

        if (is_string($param)) {
            $str = $this->resolveString($param);
            $this->resolving = [];
            return $str;
        }

        return $param;
    }

    /**
     * Resolves a string with the given parameters `$this->parameters`.
     *
     * @param string $string
     *
     * @api
     *
     * @return mixed the resolved parameter or key (string, array, etc)
     */
    public function resolveString($string)
    {
        //reduce some overhead
        if (false === strpos($string, '%')) {
            return $string;
        }

        $keys = explode(',', StringHelper::strWrap(implode('%,%', array_keys($this->parameters)), '%'));

        $this->checkReferenceViolation($string, $this->resolving);

        if (in_array($string, $keys)) {
            return $this->resolveParam($this->parameters[$key = trim($string, '%')], true);
        }

        $rkeys = array_filter(array_combine($keys, $this->parameters), function ($val) {
            if (is_string($val)) {
                return $val;
            }
        });

        $str = strtr($string, $rkeys);

        return $this->unescape(false !== strpos($string, '%') ? strtr($str, $rkeys) : $str);

    }

    /**
     * escape
     *
     * @param mixed $value
     *
     * @api
     *
     * @return mixed
     */
    public function escape($value)
    {
        if (is_string($value)) {
            return StringHelper::strEscape($value, '%');
        }

        if (is_array($value) || $value instanceof \Traversable) {
            $result = [];
            foreach ($value as $key => $val) {
                $result[$key] = $this->escape($val);
            }

            return $result;
        }

        return $value;
    }

    /**
     * unescape
     *
     * @param mixed $str
     *
     * @api
     *
     * @return mixed
     */
    public function unescape($value)
    {
        if (is_string($value)) {
            return StringHelper::strUnescape($value, '%');
        }

        if (is_array($value) || $value instanceof \Traversable) {
            $result = [];
            foreach ($value as $key => $val) {
                $result[$key] = $this->unescape($val);
            }

            return $result;
        }

        return $value;
    }

    /**
     * Sets the internal resolved state to unresolved and deletes all resolved
     * parmeters.
     *
     * Be careful with this. This method is internaly used when a parameter
     * collection is merged with another one.
     *
     * @internal
     *
     * @return mixed
     */
    public function setUnresolved()
    {
        $this->resolvedParams = [];
        $this->resolved = false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($param)
    {
        return $this->has($param);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($param)
    {
        return $this->remove($param);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($param, $value)
    {
        return $this->set($param, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Selene\Module\DI\Exception\ParameterNotFoundException
     * @return mixed
     */
    public function offsetGet($param)
    {
        return $this->get($param);
    }


    /**
     * Return a reference to the current used parameters.
     *
     * @return array Reference to $this->resolvedParams or $this->parameters
     */
    protected function &getParameters()
    {
        if ($this->resolved) {
            return $this->resolvedParams;
        }

        return $this->parameters;
    }

    /**
     * Check for circular references while resolving a value.
     *
     * @param mixed $value
     * @throws \Selene\Module\DI\Exception\ParameterResolvingException
     *
     * @return void
     */
    private function checkReferenceViolation($value, &$res = [])
    {
        if (is_string($value) && preg_match('/^%([^%\s]+)%$/', $value, $matches)) {
            if (isset($res[$key = $matches[1]])) {
                throw new ParameterResolvingException(
                    sprintf('[circular reference]: param "%s" is referencing itself', $key)
                );
            }

            $res[$key] = true;
        }

        return $res;
    }

    /**
     * doResolve
     *
     * @return void
     */
    private function doResolve()
    {
        $resolved = array();

        foreach ($this->parameters as $key => $value) {

            if (is_array($value) || is_string($value)) {
                $resolved[$key] = $this->resolveParam($value);
            } else {
                $resolved[$key] = $value;
            }
        }

        $this->resolved = true;
        $this->resolvedParams = $resolved;
    }
}
