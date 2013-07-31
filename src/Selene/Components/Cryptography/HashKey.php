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
 * @class HashKey
 * @see HashInterface
 *
 * @package Selene\Components\Cryptography
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class HashKey implements HashInterface
{
    /**
     * character storage for bcBaseConvert fallback
     *
     * @var string
     */
    protected static $storage  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * register
     *
     * @var string
     */
    protected static $register = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * hash_hmac key
     *
     * @var string
     */
    private $key;

    /**
     * @param mixed $key
     *
     * @access public
     */
    public function __construct($key = null)
    {
        $this->key = $key;
    }

    /**
     * hash
     *
     * @param mixed $value
     * @param array $options
     *
     * @access public
     * @return string
     */
    public function hash($value, array $options = null)
    {
        $options = is_array($options) ? $options : ['secret' => $this->key];
        return $this->generate($value, $options);
    }

    /**
     * check
     *
     * @param mixed $value
     * @param mixed $hash
     * @param mixed $options
     *
     * @access public
     * @return bool
     */
    public function check($value, $hash, $options = null)
    {
        if (strlen($hash) !== 22) {
            return false;
        }

        return strpos($this->hash($value, $options), $hash) !== false;
    }

    /**
     * generate
     *
     * @param mixed $value
     * @param mixed $options
     */
    private function generate($value, $options)
    {
        $base = hash_hmac('md5', $value, $options['secret']);

        if (function_exists('gmp_init')) {

            $init = gmp_init($base, 16);
            $out  = gmp_strval($init, 62);

        } else {
            $out = $this->bcBaseConvert($base, 16, 62);
        }

        return $out;
    }

    /**
     * bcBaseConvert
     *
     * @param mixed $value
     * @param mixed $sourceformat
     * @param mixed $targetformat
     */
    private function bcBaseConvert($value, $sourceformat, $targetformat)
    {

        if (max($sourceformat, $targetformat) > strlen(static::$storage)) {
            trigger_error('Bad Format max: ' . strlen(static::$storage), E_USER_ERROR);
        }

        if (min($sourceformat, $targetformat) < 2) {
            trigger_error('Bad Format min: 2', E_USER_ERROR);
        }

        $dec    = '0';
        $level  = 0;
        $result = '';
        $value  = trim((string)$value, "\r\n\t +");
        $prefix = '-' === $value{0} ? '-' : '';
        $value  = ltrim($value, "-0");
        $len    = strlen($value);

        for ($i = 0; $i < $len; $i++) {

            $val = strpos(static::$storage, $value{$len - 1 - $i});

            if (false === $val) {
                trigger_error('Bad Char in input 1', E_USER_ERROR);
            }

            if ($val >= $sourceformat) {
                trigger_error('Bad Char in input 2', E_USER_ERROR);
            }

            $dec = bcadd($dec, bcmul(bcpow($sourceformat, $i), $val));
        }

        if (10 === $targetformat) {
            return $prefix . $dec;
        }

        while (1 !== bccomp(bcpow($targetformat, $level++), $dec)) {
        }

        for ($i = ($level - 2); $i >= 0; $i--) {
            $factor  = bcpow($targetformat, $i);
            $number  = bcdiv($dec, $factor, 0);
            $dec     = bcmod($dec, $factor);
            $result .= static::$register{$number};
        }
        $result = empty($result) ? '0' : $result;
        return $prefix . $result;
    }
}
