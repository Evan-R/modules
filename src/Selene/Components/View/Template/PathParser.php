<?php

/**
 * This File is part of the Selene\Components\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

use \Selene\Components\Common\SeparatorParser;

/**
 * @class PathParser extends SeparatorParser PathParser
 * @see SeparatorParser
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PathParser extends SeparatorParser
{
    public function __construct($defaultNamespace = '__main__')
    {
        $this->defaultNamespace = $defaultNamespace;
    }

    public function parse($string)
    {
        list($namespace, $subpath, $template) = parent::parse($name = $this->prepareString($string));

        $namespace = $namespace ?: $this->defaultNamespace;

        return [
            trim($name, ':'),
            $namespace,
            ltrim(strtr($subpath, ['.' => DIRECTORY_SEPARATOR]) . DIRECTORY_SEPARATOR . $template, DIRECTORY_SEPARATOR)
        ];
    }

    /**
     * prepareString
     *
     * @param string $string
     *
     * @return string
     */
    protected function prepareString($string)
    {
        if (2 !== ($count = substr_count($string, ':'))) {
            while ($count++ < 2) {
                $string = ':'.$string;
            }
        }

        return $string;
    }
}
