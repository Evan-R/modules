<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

use \Selene\Module\Config\Validator\Exception\ValueUnsetException;
use \Selene\Module\Config\Validator\Exception\ValidationException;

/**
 * @class Condition
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Condition
{
    /**
     * node
     *
     * @var NodeInterface
     */
    protected $node;

    /**
     * result
     *
     * @var \Closure
     */
    protected $result;

    /**
     * condition
     *
     * @var \Closure
     */
    protected $condition;

    /**
     * Constructor.
     *
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * getResult
     *
     * @return Closure
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * getCondition
     *
     *
     * @return Closure
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * always
     *
     * @return Condition
     */
    public function always(\Closure $result)
    {
        $this->condition = function () {
            return true;
        };

        $this->result = $result;

        return $this;
    }

    /**
     * When condition
     *
     * @see ifTrue
     * @return Condition
     */
    public function when(\Closure $condition)
    {
        return $this->ifTrue($condition);
    }

    /**
     * ifTrue
     *
     * @return Condition
     */
    public function ifTrue(\Closure $callback)
    {
        $this->condition = $callback;

        return $this;
    }

    /**
     * ifTrue
     *
     * @return Condition
     */
    public function ifMissing()
    {
        $this->condition = function ($value) {
            return $value instanceof MissingValue;
        };

        return $this;
    }

    /**
     * ifInArray
     *
     * @param array $values
     *
     * @return Condition
     */
    public function ifInArray(array $values)
    {
        $this->condition = function ($value) use ($values) {
            return in_array($value, $values);
        };

        return $this;
    }

    /**
     * ifNotInArray
     *
     * @param array $values
     *
     * @return Condition
     */
    public function ifNotInArray(array $values)
    {
        $this->condition = function ($value) use ($values) {
            return !in_array($value, $values);
        };

        return $this;
    }

    /**
     * ifEmpty
     *
     * @return Condition
     */
    public function ifEmpty()
    {
        $this->condition = function ($value) {
            return empty($value);
        };

        return $this;
    }

    /**
     * ifNull
     *
     * @return Condition
     */
    public function ifNull()
    {
        $this->condition = function ($value) {
            return null === $value;
        };

        return $this;
    }

    /**
     * ifArray
     *
     * @return Condition
     */
    public function ifArray()
    {
        $this->condition = function ($value) {
            return is_array($value);
        };

        return $this;
    }

    /**
     * ifNoArray
     *
     * @return Contition
     */
    public function ifNotArray()
    {
        $this->condition = function ($value) {
            return !is_array($value);
        };

        return $this;
    }

    /**
     * ifNoArray
     *
     * @return Contition
     */
    public function ifString()
    {
        $this->condition = function ($value) {
            return is_string($value);
        };

        return $this;
    }

    /**
     * ifNoArray
     *
     * @return Contition
     */
    public function ifInt()
    {
        $this->condition = function ($value) {
            return is_int($value);
        };

        return $this;
    }

    /**
     * ifNoArray
     *
     * @return Contition
     */
    public function ifFloat()
    {
        $this->condition = function ($value) {
            return is_float($value);
        };

        return $this;
    }

    /**
     * then
     *
     * @param callable $result
     *
     * @return Contition
     */
    public function then(\Closure $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * thenUnset
     *
     * @return Contition

     */
    public function thenRemove()
    {
        $this->result = function () {
            throw new ValueUnsetException;
        };

        return $this;
    }

    /**
     * thenMarkInvalid
     *
     * @param string $message
     *
     * @return Contition
     */
    public function thenMarkInvalid($message = null)
    {
        $this->result = function ($value) use ($message) {
            $value = is_scalar($value) ? $value : json_encode($value);
            $exp = $message ?
                new ValidationException(sprintf($message, $value)) : ValidationException::invalidValue($value);
            throw $exp;
        };

        return $this;
    }

    /**
     * merge
     *
     * @param Condition $condition
     *
     * @return void
     */
    public function copy(Condition $condition)
    {
        $this->condition = $condition->getCondition();
        $this->result    = $condition->getResult();
    }

    /**
     * Run the condition.
     *
     * @param mixed|null $value
     *
     * @return mixed
     */
    public function run($value = null)
    {
        $node = $this->node;

        return call_user_func($this->condition, $value, $node) ? call_user_func($this->result, $value, $node) : null;
    }

    /**
     * Returns the node of the condition.
     *
     * @throws \InvalidArgumentException if no condition or result is set.
     * @return void
     */
    public function end()
    {
        if (!$this->condition) {
            throw new \InvalidArgumentException(
                sprintf('Condition of node "%s" is not declared.', $this->node->getFormattedKey())
            );
        }

        if (!$this->result) {
            throw new \InvalidArgumentException(
                sprintf('Condition of node "%s" has no result.', $this->node->getFormattedKey())
            );
        }

        return $this->node;
    }
}
