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
    public function __construct($defaultNamespace = '__root__')
    {
        $this->defaultNamespace = $defaultNamespace;
    }

    public function getDefaultNamespace()
    {
        return $this->defaultNamespace;
    }

    public function parse($string)
    {
        list($namespace, $subpath, $template) = parent::parse($name = $this->prepareString($string));

        $namespace = $namespace ?: $this->defaultNamespace;

        $ds = DIRECTORY_SEPARATOR;

        return [
            strtr(trim($name, ':'), ['\\' => '.', '/' => '.']),
            $namespace,
            ltrim(strtr($subpath, ['.' => $ds, ':' => $ds]) . DIRECTORY_SEPARATOR . $template, '\\/')
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

        return trim($string);
    }
}
