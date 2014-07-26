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

use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class DictNode extends ArrayNode implements \Iterator
 * @see \Iterator
 * @see ArrayNode
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DictNode extends ArrayNode implements \Iterator
{
    const KEYS_STRICT    = true;
    const KEYS_NONSTRICT = false;

    /**
     * key
     *
     * @var string
     */
    protected $key;

    /**
     * leastKeys
     *
     * @var array
     */
    protected $hasAtLeast;

    /**
     * current
     *
     * @var int
     */
    private $current;

    /**
     * keySeparator
     *
     * @var string
     */
    private static $keySepearator = '-::-';

    /**
     * pattern
     *
     * @var string
     */
    private static $pattern = '~-::-[0-9]+-::-~';

    /**
     * Create a new DictNode object.
     * @access public
     */
    public function __construct($mode = self::KEYS_NONSTRICT)
    {
        $this->mode = $mode;

        $this->current = 0;
        $this->requiredKeys = [];
        parent::__construct();
    }

    /**
     * The node must container at least one of the given keys.
     *
     * @access public
     * @return mixed
     */
    public function atLeastOne()
    {
        $this->hasAtLeast = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = $this->concatKeys($value, static::$keySepearator);

        return is_array($value) && !(bool)preg_match(static::$pattern, $keys);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value = null)
    {
        $valid = parent::validate();

        $value = $this->getValue();

        if (self::KEYS_STRICT === $this->mode) {
            $this->checkExceedingKeys($value);
        }

        $this->validateLeastKeys((array)$value);

        return $valid;
    }

    /**
     * validateLeastKeys
     *
     * @param array $values
     *
     * @throws ValidationException
     * @access protected
     * @return void
     */
    protected function validateLeastKeys(array $values)
    {
        if (!$this->hasAtLeast) {
            return;
        }

        $keys = $this->getKeys();

        foreach (array_keys($values) as $key) {
            if (!in_array($key, $keys)) {
                throw new ValidationException(
                    sprintf(
                        '%s must contain at least one of these keys: "%s"',
                        $this->getKey(),
                        implode('", "', $keys)
                    )
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->children[$this->current];
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->children[$this->current]->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->children[$this->current]);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->current = 0;
    }

    /**
     * mergeValue
     *
     * @param mixed $value
     *
     * @return void
     */
    public function mergeValue($value)
    {
        return array_merge($this->value, (array)$value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('%s may not contain numeric keys', $this->getKey());
    }

    /**
     * Concatenate all array keys
     *
     * @param array $value
     *
     * @access private
     * @return string
     */
    private function concatKeys(array $value, $separator)
    {
        $keys = array_keys($value);

        array_push($keys, $separator);
        array_unshift($keys, $separator);

        return implode($separator, $keys);
    }

    /**
     * checkExceedingKeys
     *
     * @param array $value
     *
     * @throws ValidationException
     * @access protected
     * @return void
     */
    protected function checkExceedingKeys(array $value)
    {
        foreach ((array)$value as $key => $val) {
            if (null === $this->getChildByKey($key)) {
                throw new ValidationException(
                    sprintf('invalid key %s in %s', $key, $this->getKey())
                );
            }
        }
    }
}
