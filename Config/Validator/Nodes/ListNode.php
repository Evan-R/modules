<?php

/**
 * This File is part of the Selene\Components\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;

/**
 * @class Collection
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class ListNode extends ArrayNode implements \IteratorAggregate
{
    /**
     * validating
     *
     * @var bool|null
     */
    protected $validating;


    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        if (!is_array($value)) {
            return false;
        }

        return !empty($value) ? ctype_digit(implode('', array_keys($value))) : true;
    }

    /**
     * {@inheritdoc}
     *
     * @return ListNode
     */
    public function addChild(NodeInterface $node)
    {
        if (!$this->validating && 0 !== count($this->getChildren())) {
            throw new \BadMethodCallException(sprintf('ListNode %s already as a node declaration.', $this->getFormattedKey()));
        }

        $node->setKey(count($this->children));

        return parent::addChild($node);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->validating = true;

        if ($child = $this->getFirstChild()) {

            $this->children->detachAll();

            if (is_array($value = $this->getValue())) {
                foreach ($value as $i => $val) {
                    $this->addChild(clone($child));
                }
            }
        }

        $valid = parent::validate();

        $this->validating = false;

        return $valid;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new $this->getChildren();
    }

    /**
     * {@inheritdoc}
     */
    protected function mergeValues(array $values, array $results)
    {
        foreach ($values as $i => $val) {
            $values[$i] = $results[$i];
        }

        return $values;
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
        if (!is_array($value)) {
            parent::handleTypeError($value);
        }

        $keys = $this->getInvalidKeys($value);
        $string = implode('", "', $keys);

        throw new InvalidTypeException(
            sprintf('Invalid key%s "%s" in %s', count($keys) < 2 ? '' : 's', $string, $this->getFormattedKey())
        );
    }

    /**
     * getInvalidKeys
     *
     * @param array $values
     *
     * @access private
     * @return string
     */
    private function getInvalidKeys(array $values)
    {
        $keys = array_keys($values);

        $strings = array_filter($keys, function ($key) {
            return !is_int($key);
        });

        return $strings;
    }

}
