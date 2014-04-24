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

/**
 * @class DictNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class DictNode extends ArrayNode implements \Iterator
{
    private $current;

    protected $key;

    public function __construct()
    {
        $this->current = 0;
        parent::__construct();
    }

    /**
     * validateType
     *
     * @param mixed $value
     *
     * @access public
     * @return boolean
     */
    public function validateType($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = '-::-'.implode('-::-', array_keys($value)).'-::-';
        return is_array($value) && !preg_match('/-::-[0-9]+-::-/', $keys);
    }

    public function validate($value = null)
    {
        $valid = parent::validate($value);

        foreach ((array)$value as $key => $val) {
            if (null === $this->getChildByKey($key)) {
                throw new ValidationException(
                    sprintf('invalid key %s in %s', $key, $this->getKey())
                );
            }
        }

        return $valid;
    }


    public function current()
    {
        return $this->children[$this->current];
    }

    public function key()
    {
        return $this->children[$this->current]->getKey();
    }

    public function valid()
    {
        return isset($this->children[$this->current]);
    }

    public function next()
    {
        $this->current++;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('%s my not contain numeric keys', $this->getKey());
    }
}
