<?php

/**
 * This File is part of the Stream\Cryptography package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */
namespace Selene\Components\Cryptography;

/**
 * @class HashBcrypt
 * @see HashInterface
 *
 * @package Selene\Components\Cryptography
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class HashBcrypt implements HashInterface
{
    /**
     * defult
     *
     * @var string
     */
    private static $default = ['cost' => 7];

    /**
     * hash
     *
     * @param mixed $password
     * @param array $options
     */
    public function hash($password, array $options = null)
    {
        $options = is_array($options) ? array_merge(static::$default, $options) : static::$default;
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * check
     *
     * @param mixed $password
     * @param mixed $hash
     */
    public function check($password, $hash, $options = null)
    {
        return password_verify($password, $hash);
    }
}
