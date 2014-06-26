<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

/**
 * @interface FileTargetInterface
 * @package Selene\Components\Package
 * @version $Id$
 */
interface FileTargetInterface
{
    /**
     * getFilename
     *
     * @return string
     */
    public function getFilename();

    /**
     * getContents
     *
     * @return string
     */
    public function getContents();

    /**
     * getRelativePath
     *
     * @return string
     */
    public function getRelativePath();

    /**
     * isValid
     *
     * @return boolean
     */
    public function isValid();
}
