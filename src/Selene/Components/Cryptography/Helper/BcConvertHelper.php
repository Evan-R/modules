<?php

/**
 * This File is part of the Selene\Components\Cryptography\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cryptography\Helper;

/**
 * @class BcConvertHelper BcConvertHelper
 *
 * @package Selene\Components\Cryptography\Helper
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class BcConvertHelper
{
    /**
     * character storage for bcBaseConvert fallback
     *
     * @var string
     */
    private static $storage  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * register
     *
     * @var string
     */
    private static $register = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * baseConvert
     *
     * @param mixed $value
     * @param mixed $sourceformat
     * @param mixed $targetformat
     *
     * @return string;
     */
    public static function baseConvert($value, $sourceformat, $targetformat)
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

    private function __construct()
    {
    }
}
