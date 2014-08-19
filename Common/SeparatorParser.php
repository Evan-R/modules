<?php

/*
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common;

/**
 * @class SeqenceParser
 * @package Selene\Module\Common
 * @version $Id$
 */
class SeparatorParser implements SeparatorParserInterface
{
    /**
     * separator
     *
     * @var string
     */
    protected static $separator = ':';

    /**
     * supports
     *
     * @param mixed $string
     *
     * @access public
     * @return boolean
     */
    public function supports($string)
    {
        return 2 === substr_count($string, static::$separator);
    }

    /**
     * parse
     *
     * @param mixed $string
     *
     * @access public
     * @return array
     */
    public function parse($string)
    {
        list($first, $mid, $last) = array_pad(explode(static::$separator, $string), 3, null);

        //var_dump($first, $mid, $last);
        return [$first, $mid, $last];
    }
}
