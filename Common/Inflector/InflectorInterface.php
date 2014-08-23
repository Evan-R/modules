<?php

/*
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Inflector;

/**
 * @interface InflectorInterface
 *
 * @package Selene\Module\Common\Inflector
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface InflectorInterface
{
    /**
     * Pluralizes a string.
     *
     * @param string $value
     *
     * @return string
     */
    public function pluralize($value);

    /**
     * singularizes as a string.
     *
     * @param string $value
     *
     * @return string
     */
    public function singularize($value);
}
