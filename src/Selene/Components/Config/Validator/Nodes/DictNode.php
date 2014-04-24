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
    /**
     * key
     *
     * @var string
     */
    protected $key;

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
    public function __construct()
    {
        $this->current = 0;
        parent::__construct();
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
        $valid = parent::validate($value);

        $this->checkExceedingKeys($value);

        return $valid;
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
