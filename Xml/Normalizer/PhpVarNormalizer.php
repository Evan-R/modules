<?php

/**
 * This File is part of the \Users\malcolm\www\selene_source\src\Selene\Module\Xml\Normalizer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Normalizer;

/**
 * @class PhpVarNormalizer
 * @package \Users\malcolm\www\selene_source\src\Selene\Module\Xml\Normalizer
 * @version $Id$
 */
class PhpVarNormalizer extends Normalizer
{
    /**
     * normalizeString
     *
     * @param mixed $string
     * @access protected
     * @return mixed
     */
    protected function normalizeString($string)
    {
        if (0 < substr_count($value = parent::normalizeString($string), '-')) {
            return strtr($value, ['-' => '_']);
        }
    }
}
