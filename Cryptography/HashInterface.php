<?php

/**
 * This File is part of the Selene\Components\Cryptography package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cryptography;

interface HashInterface
{
    /**
     * create hash from string
     *
     * @param string $string  string to be hased
     * @param array  $options optional configuration
     */
    public function hash($string, array $options = null);

    /**
     * check an input value against a hash
     *
     * @param string $string value to be compared agains hash
     * @param string $hash   hash to be compared against string
     */
    public function check($string, $hash, $options = null);
}
