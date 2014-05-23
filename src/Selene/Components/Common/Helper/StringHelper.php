<?php

/**
 * This File is part of the Selene\Components\Common\Helper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Helper;

/**
 * @class StringHelper
 *
 * @package Selene\Components\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class StringHelper
{
    /**
     * rchars
     *
     * @var string
     */
    private static $rchars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Safe compare two strings to prevent timing attacks.
     *
     * You should consider using `password_veryfy()` anyway.
     *
     * @see http://codereview.stackexchange.com/questions/13512/
     *  constant-time-string-comparision-in-php-to-prevent-timing-attacks
     * @see http://www.php.net/manual/en/function.password-verify.php
     *
     * @param string $string a knowen string
     * @param string $input  a user input string
     *
     * @access public
     * @return boolean
     */
    public static function strSafeCompare($string, $input)
    {
        $pad = static::strRand(4);

        $string .= $pad;
        $input  .= $pad;

        $strLen = strlen($string);
        $inpLen = strlen($input);

        $result = $strLen ^ $inpLen;

        for ($i = 0; $i < $inpLen; $i++) {
            $result |= (ord($string[$i % $strLen]) ^ ord($input[$i]));
        }

        return 0 === $result;
    }

    /**
     * strEquals
     *
     * @param mixed $string
     * @param mixed $input
     *
     * @access public
     * @return boolean
     */
    public static function strEquals($string, $input)
    {
        return 0 === strcmp($string, $input);
    }

    /**
     * Nulls an empty string if input is a string.
     *
     * Only vaiables can be passed as argument.
     *
     * @param mixed $input
     *
     * @access public
     * @return mixed|null
     */
    public static function strNull(&$input)
    {
        return is_string($input) ? (0 === strlen($input) ? null : $input) : $input;
    }

    /**
     * strRand
     *
     * @param mixed $length
     *
     * @access public
     * @return string
     */
    public static function strRand($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'StringHelper::strRand() expects first argument to be integer, instead saw %s.',
                    gettype($length)
                )
            );
        }

        if (!function_exists('openssl_random_pseudo_bytes')) {
            return self::strQuickRand($length);
        }

        if (null === ($bytes = openssl_random_pseudo_bytes($length * 2))) {
            throw new \RuntimeException('Cannot generate random string');
        }

        return substr(str_replace(['/', '=', '+'], '', base64_encode($bytes)), 0, $length);
    }

    /**
     * strQuickRand
     *
     * @param mixed $length
     *
     * @access public
     * @return string
     */
    public static function strQuickRand($length)
    {
        return substr(str_shuffle(str_repeat(static::$rchars, 5)), 0, $length);
    }

    /**
     * strWrap
     *
     * @param mixed $str
     * @param mixed $begin
     * @param mixed $end
     *
     * @access public
     * @return string
     */
    public static function strWrap($str, $begin, $end = null)
    {
        return sprintf('%s%s%s', $begin, $str, $end ?: $begin);
    }

    /**
     * strUnescape
     *
     * @param mixed $str
     * @param mixed $needle
     *
     * @access public
     * @return string
     */
    public static function strUnescape($str, $needle)
    {
        return str_replace($needle.$needle, $needle, $str);
    }

    /**
     * strEscape
     *
     * @param mixed $str
     * @param mixed $needle
     *
     * @access public
     * @return string
     */
    public static function strEscape($str, $needle)
    {
        return str_replace($needle, $needle.$needle, $str);
    }

    /**
     * substrSplit
     *
     * @param mixed $string
     * @param mixed $pos
     *
     * @access public
     * @return array
     */
    public static function strrposSplit($string, $char)
    {
        $pos = strrpos($string, $char);

        return [substr($string, 0, $pos), substr($string, $pos + 1)];
    }

    /**
     * strposSplit
     *
     * @param mixed $string
     * @param mixed $char
     *
     * @access public
     * @return array
     */
    public static function strposSplit($string, $char)
    {
        $pos = strpos($string, $char);

        return [substr($string, 0, $pos), substr($string, $pos + 1)];
    }

    /**
     * strPad
     *
     * @param mixed $str
     * @param int $len
     *
     * @access public
     * @return sring
     */
    public static function strPad($str, $len = 1)
    {
        return $str . str_repeat(chr(0), $len);
    }

    /**
     * strConcat
     *
     * @access public
     * @return string
     */
    public static function strConcat($string, $char)
    {
        return vsprintf(str_repeat('%s', count($args = func_get_args())), $args);
    }

    /**
     * substrBefore
     *
     * @param mixed $char
     * @param mixed $string
     *
     * @access public
     * @return string|boolean
     */
    public static function substrBefore($string, $char)
    {
        return false !== ($pos = strpos($string, $char)) ? substr($string, 0, $pos) : false;
    }

    /**
     * substriBefore
     *
     * @access public
     * @return string|boolean
     */
    public static function substriBefore($string, $char)
    {
        return false !== ($pos = stripos($string, $char)) ? substr($string, 0, $pos) : false;
    }

    /**
     * Returns the substring after the first occurance of a given character
     *
     * @param string $string
     * @param string $char
     *
     * @access public
     * @return string|boolean
     */
    public static function substrAfter($string, $char)
    {
        return false !== ($pos = strpos($string, $char)) ? substr($string, $pos + 1) : false;
    }

    /**
     * Returns the substring after the first occurance of a given character
     * (case insensitive)
     *
     * @param string $string
     * @param string $char
     *
     * @access public
     * @return string|boolean
     */
    public static function substriAfter($string, $char)
    {
        return false !== ($pos = stripos($string, $char)) ? substr($string, $pos + 1) : false;
    }

    /**
     * Determine if a string contains a gicen string sequence
     *
     * @param string $string
     * @param string $sequence
     *
     * @access public
     * @return boolean
     */
    public static function strContains($string, $sequence)
    {
        return false !== strpos($string, $sequence);
    }

    /**
     * Like strContainers but case insensitive.
     *
     * @see StringHelper::strContains
     *
     * @param string $string
     * @param string $sequence
     *
     * @access public
     * @return boolean
     */
    public static function striContains($string, $sequence)
    {
        return false !== stripos($string, $sequence);
    }

    /**
     * determine if a string starts wiht a given sequence
     *
     * @param string $string
     * @param string $sequence
     *
     * @return boolean
     */
    public static function strStartsWith($string, $sequence)
    {
        return 0 === strpos($string, $sequence);
    }

    /**
     * determine if a string starts wiht a given sequence
     *
     * @param string $string
     * @param string $sequence
     *
     * @return boolean
     */
    public static function striStartsWith($string, $sequence)
    {
        return 0 === stripos($string, $sequence);
    }

    /**
     * determine if a string ends wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    public static function strEndsWith($string, $sequence)
    {
        return 0 === strcmp($sequence, (substr($string, - strlen($sequence))));
    }

    /**
     * determine if a string ends wiht a given sequence
     *
     * @param string $string
     * @param string $sequence
     *
     * @return boolean
     */
    public static function striEndsWith($string, $sequence)
    {
        return 0 === strcasecmp($sequence, (substr($string, - strlen($sequence))));
    }

    /**
     * convert camelcase to low dash notation
     *
     * @param string $string
     *
     * @return string
     */
    public static function strLowDash($string)
    {
        return strtolower(preg_replace('#[A-Z]#', '_$0', lcfirst($string)));
    }

    /**
     * camelcase notataion
     *
     * @param mixed $str
     * @param array $replacement
     *
     * @access public
     * @return string
     */
    public static function strCamelCase($str, $replacement = ['-' => ' ', '_' => ' '])
    {
        return lcfirst(self::strCamelCaseAll($str, $replacement));
    }

    /**
     * all camelcase notataion
     *
     * @param mixed $string
     * @param array $replacement
     *
     * @access public
     * @return string
     */
    public static function strCamelCaseAll($string, array $replacement = ['-' => ' ', '_' => ' '])
    {
        return strtr(ucwords(strtr($string, $replacement)), [' ' => '']);
    }

    private function __construct()
    {
        return null;
    }
}
