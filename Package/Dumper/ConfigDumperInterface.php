<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Dumper;

/**
 * @interface ConfigDumperInterface
 * @package Selene\Components\Package
 * @version $Id$
 */
interface ConfigDumperInterface
{
    /**
     * supports
     *
     * @param mixed $format
     *
     * @return boolean
     */
    public function supports($format);

    /**
     * getFilename
     *
     * @return string
     */
    public function getFilename();

    /**
     * dump
     *
     * @param string $name
     * @param array $contents
     *
     * @return string
     */
    public function dump($name, array $contents = [], $format = null);
}
