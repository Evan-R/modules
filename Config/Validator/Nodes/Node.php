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

use \Selene\Module\Config\Validator\Builder;
use \Selene\Module\Config\Validator\Exception\ValueUnsetException;
use \Selene\Module\Config\Validator\Exception\ValidationException;
use \Selene\Module\Config\Validator\Exception\InvalidTypeException;
use \Selene\Module\Config\Validator\Exception\MissingValueException;

/**
 * @abstract class Node
 * @see NodeInterface
 *
 * @package Selene\Module\Config
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
     * @var boolean
     */
    protected $allowEmpty;

    /**
     * builder
     *
     * @var Builder
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
     * @var boolean
     */
    private $invalid;

    /**
     * Constructor.
     *
     * @param NodeInterface|null $parent
     */
    public function __construct(NodeInterface $parent = null)
    {
        $this->required   = true;
        $this->allowEmpty = true;
        $this->finalized  = false;
        $this->conditions = [];
    }

    /**
     * Set the tree builder.
     *
     * @param Builder $builder
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
     * Sets the required status.
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
     * Get the value after the node's been finalized.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Open a condition block
     *
     * @return Condition
     */
    final public function condition()
    {
        $this->addCondition($condition = new Condition($this));

        return $condition;
    }

    /**
     * addCondition
     *
     * @param Condition $condition
     *
     * @return void
     */
    public function addCondition(Condition $condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * Gets all conditions.
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
     * @return Builder
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
     * @return NodeInterface this instance
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
     * @return NodeInterface the parent node
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
     * @return NodeInterface the parent node
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
     * Finalize the node
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
     *
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

        // if key is missing and none optional, throw exception.
        if ($missing && !$this->isOptional()) {
            throw MissingValueException::missingValue($this->getFormattedKey());
        // Otherwise, if the value is considered empty and empty values are not
        // allowed, throw exception.
        } elseif ($empty && !$this->allowEmpty) {
            throw ValidationException::notEmpty($this->getFormattedKey());
        }

        // don't allow MissingValue to slip through
        if ($this->isOptional() && $missing) {
            $this->value = $value = null;
        }

        // only typecheck value if is required but empty and/or missing.
        if (!$valid && !$empty && !$missing) {
            return $this->handleTypeError($value);
        }

        return true;
    }

    /**
     * Get the formatted key of this node.
     *
     * The key is formatted using the node's parent node, so e.g. a node with
     * key of bar and a parent node with the key of foo will result in `foo[bar]`.
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
     * Get the node type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Check if the given value is considered empty.
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
     * Do prevalidation
     *
     * Should be called on finalieze.
     *
     * @param mixed $value
     *
     * @return mixed|null
     */
    protected function preValidate(&$value)
    {
        if ($this->isOptional() && $value instanceof MissingValue) {
            $value = null !== ($default = $this->getDefault()) ? $default : $value;
        }

        foreach ($this->conditions as $condition) {
            $this->runCondition($condition, $value);
        }
    }

    /**
     * Execute a condition callback.
     *
     * @param Condition $condition
     * @param mixed $value
     *
     * @return void
     */
    protected function runCondition(Condition $condition, &$value = null)
    {
        try {
            if (null !== ($result = $condition->run($value))) {
                $value = $result;
            }
        } catch (ValueUnsetException $e) {
            $value = null;
            //$this->getParent()->removeChild($this);
            $this->removeParent();
        } catch (ValidationException $e) {
            $this->markInvalid($e);
        }
    }

    /**
     * The exception thrown if there's a typeerror on validation.
     *
     * @param mixed $value
     *
     * @throws InvalidTypeException
     * @return void
     */
    protected function handleTypeError($value)
    {
        throw InvalidTypeException::invalidType($this, $value);
    }

    /**
     * Mark node as invalid.
     *
     * Will always trigger the node to be invalid.
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
     * Checks if node is marked as invalid.
     *
     * @return boolean
     */
    final protected function isMarkedInvalid()
    {
        return $this->invalid instanceof ValidationException;
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
     * Handles calls on the Builder instance.
     *
     * @param string $method
     * @param array  $arguments
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
}
