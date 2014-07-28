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
class DictNode extends ArrayNode
{
    const KEYS_STRICT = true;
    const KEYS_LOOSE  = false;

    /**
     * keySeparator
     *
     * @var string
     */
    private static $keySeparator = '-::-';

    /**
     * pattern
     *
     * @var string
     */
    private static $pattern = '~-::-[0-9]+-::-~';

    /**
     * Create a new DictNode object.
     *
     * @param boolean $mode
     */
    public function __construct($mode = self::KEYS_LOOSE)
    {
        $this->mode = $mode;

        $this->requiredKeys = [];
        parent::__construct();
    }

    /**
     * Finds a child node by its key.
     *
     * @param string $key
     *
     * @return NodeInterface|null
     */
    public function findChildByKey($key)
    {
        return $this->children->find(function ($node) use ($key) {
            return $key === $node->getKey();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = $this->concatKeys($value, static::$keySeparator);

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
            return $this->checkExceedingKeys($value);
        }

        return $valid;
    }

    /**
     * Concatenate all array keys
     *
     * @param array $value
     *
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
     * @return boolean
     */
    protected function checkExceedingKeys(array $value)
    {
        foreach ((array)$value as $key => $val) {

            if (null === $this->findChildByKey($key)) {
                throw new ValidationException(
                    sprintf('Invalid key "%s" in %s.', $key, $this->getFormattedKey())
                );
            }
        }

        return true;
    }
}
