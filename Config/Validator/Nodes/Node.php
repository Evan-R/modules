<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

use \Selene\Components\Config\Validator\Builder;
use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;
use \Selene\Components\Config\Validator\Exception\MissingValueException;
use \Selene\Components\Config\Validator\Exception\ValueUnsetException;

/**
 * @abstract class Node implements NodeInterface
 * @see NodeInterface
 * @abstract
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class Node implements NodeInterface
{
    /**
     * key
     *
     * @var string
     */
    protected $key;

    /**
     * default
     *
     * @var mixed
     */
    protected $default;

    /**
     * required
     *
     * @var boolean
     */
    protected $required;

    /**
     * allowEmpty
     *
     * @var mixed
     */
    protected $allowEmpty;

    /**
     * builder
     *
     * @var mixed
     */
    protected $builder;

    /**
     * parent
     *
     * @var NodeInterface
     */
    protected $parent;

    /**
     * conditions
     *
     * @var array
     */
    protected $conditions;

    /**
     * value
     *
     * @var mixed
     */
    protected $value;

    /**
     * finalized
     *
     * @var boolean
     */
    protected $finalized;

    /**
     * invalid
     *
     * @var void
     */
    private $invalid;

    /**
     * Constructor.
     *
     * @param NodeInterface $parent
     */
    public function __construct(NodeInterface $parent = null)
    {
        $this->required   = true;
        $this->allowEmpty = true;
        $this->finalized  = false;
        $this->conditions = [];
    }

    /**
     * Clone the node.
     *
     * @return void
     */
    public function __clone()
    {
        $this->value     = null;
        $this->invalid   = null;
        $this->finalized = false;

        $conditions = [];

        foreach ($this->conditions as $condition) {

            $c = new Condition($this);
            $c->copy($condition);

            $conditions[] = $c;
        }

        $this->conditions = $conditions;
    }

    /**
     * setBuilder
     *
     * @param mixed $builder
     *
     * @return NodeInterface
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Set the parent node.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface this instance
     */
    public function setParent(NodeInterface $node)
    {
        $this->parent =& $node;
        $node->addChild($this);

        if (null !== ($builder = $this->parent->getBuilder()) && $builder !== $this->builder) {
            $this->setBuilder($builder);
        }

        return $this;
    }

    /**
     * Remove the node from a parent node.
     *
     * @return NodeInterface
     */
    public function removeParent()
    {
        $parent = $this->parent;
        $this->parent = null;

        if ($parent) {
            $parent->removeChild($this);
        }

        return $this;
    }

    /**
     * Mark this node as optional.
     *
     * @return Node
     */
    public function optional()
    {
        $this->setRequired(false);

        return $this;
    }

    /**
     * Sets the default value of this node.
     *
     * @return NodeInterface this instance
     */
    public function defaultValue($value)
    {
        $this->default = $value;

        return $this;
    }

    /**
     * setRequired
     *
     * @param bool $required
     *
     * @return NodeInterface
     */
    public function setRequired($required)
    {
        $this->required = (bool)$required;

        return $this;
    }

    /**
     * Check if this node is optional.
     *
     * @return boolean
     */
    public function isOptional()
    {
        return false === $this->required;
    }

    /**
     * Get the default value if any.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * getValue
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * condition
     *
     * @return Condition
     */
    final public function condition()
    {
        $condition = new Condition($this);

        return $this->conditions[] = $condition;
    }

    /**
     * getConditions
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Get the builder instance.
     *
     * @return \Selene\Components\Config\Validator\Builder
     */
    public function getBuilder()
    {
        if ($this->hasParent()) {
            return $this->getParent()->getBuilder();
        }

        return $this->builder;
    }

    /**
     * Set the key of the node.
     *
     * @param string|int $name
     *
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface this instance
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key of the node.
     *
     * @return string|int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get the parent node.
     *
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface the parent node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Check if this node has a parent node.
     *
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * Do not allow empty values.
     *
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface the parent node
     */
    public function notEmpty()
    {
        $this->allowEmpty = false;

        return $this;
    }

    /**
     * Validates the type of the node.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    abstract public function validateType($value);

    /**
     * finalize
     *
     * @param mixed $value
     *
     * @return NodeInterface
     */
    public function finalize($value = null)
    {
        $this->value = $value; //null !== $value ? $value : $this->getDefault();

        $this->preValidate($this->value);
        $this->finalized = true;

        return $this;
    }

    /**
     * Validates a value against the nodes definition.
     *
     * @throws MissingValueException if value is missing or empty.
     * @throws InvalidTypeException  if value has the wrong type.
     * @throws ValidationException   if marked invalid due to condition.
     * @return boolean
     */
    public function validate()
    {
        if (!$this->finalized) {
            throw new \BadMethodCallException(
                sprintf('Node %s must be finalized before validation.', $this->getFormattedKey())
            );
        }

        if ($this->isMarkedInvalid()) {
            throw new ValidationException(sprintf('%s: %s', $this->getFormattedKey(), $this->invalid->getMessage()));
        }

        if ($missing = (($value = $this->getValue()) instanceof MissingValue)) {
            $value = $value->getValue();
        }

        $empty = $missing ? false : $this->isEmptyValue($value);
        $valid = $this->validateType($value);

        if ($missing && !$this->isOptional()) {
            throw MissingValueException::missingValue($this->getFormattedKey());
        } elseif ($empty && !$this->allowEmpty) {
            throw ValidationException::notEmpty($this->getFormattedKey());
        }

        if (!$valid && !$empty && !$missing) {
            return $this->handleTypeError($value);
        }

        return true;
    }

    /**
     * getFormattedKey
     *
     * @return string
     */
    public function getFormattedKey()
    {
        if (!$this->hasParent()) {
            return $this->getKey();
        }

        $keys = [$this->getKey()];
        $node = $this;

        while ($node->hasParent()) {
            $node = $node->getParent();
            $keys[] = $key = $node->getKey();
        }

        $keys = array_reverse($keys);
        $key  = array_shift($keys);

        return empty($keys) ? $key : $key . '['.implode('][', $keys).']';
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * isEmptyValue
     *
     * @param mixed $value
     *
     * @return boolean
     */
    protected function isEmptyValue($value = null)
    {
        return is_string($value) ? (!strlen(trim($value)) > 0) : empty($value);
    }

    /**
     * __call
     *
     * @param mixed $method
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (($builder = $this->getBuilder()) && method_exists($builder, $method)) {
            return call_user_func_array([$builder, $method], $arguments);
        }

        throw new \BadMethodCallException(
            sprintf('Node %s: no builder set or method %s() does not exist.', $this->getFormattedKey(), $method)
        );
    }

    /**
     * prevalidate
     *
     * @param mixed $value
     *
     * @return mixed|null
     */
    protected function preValidate(&$value)
    {
        if ($this->isOptional() && $value instanceof MissingValue) {
            $value = ($default = $this->getDefault()) ?: $value;
        }

        foreach ($this->conditions as $condition) {
            $this->runCondition($condition, $value);
        }
    }

    /**
     * runCondition
     *
     * @param Condition $condition
     * @param mixed $value
     *
     * @return mixed|null
     */
    protected function runCondition(Condition $condition, &$value = null)
    {
        try {
            if ($result = $condition->run($value)) {
                $value = $result;
            }
        } catch (ValueUnsetException $e) {
            $value = null;
            $this->removeParent();
        } catch (ValidationException $e) {
            $this->markInvalid($e);
        }
    }

    /**
     * handleTypeError
     *
     * @param mixed $value
     *
     * @return void
     */
    protected function handleTypeError($value)
    {
        throw InvalidTypeException::invalidType($this, $value);
    }

    /**
     * markInvalid
     *
     * @param ValidationException $exception
     *
     * @return void
     */
    final protected function markInvalid(ValidationException $exception)
    {
        $this->invalid = $exception;
    }

    /**
     * isMarkedInvalid
     *
     * @return boolean
     */
    final protected function isMarkedInvalid()
    {
        return $this->invalid instanceof ValidationException;
    }
}
