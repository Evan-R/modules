<?php

/**
 * This File is part of the Selene\Components\DI\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Stubs;

/**
 * @class DocComent
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class DocComment extends Stub
{

    public function __construct($short, $long = null, array $annotations = [], $indent = 4)
    {
        $this->short = $short;
        $this->long = $long;
        $this->indent = $indent;
        $this->annotations = $annotations;
    }

    public function dump()
    {
        return $this->getComment();
    }

    public function getComment()
    {
        $indent = $this->indent($this->indent);
        $lines = [];

        $lines[] = sprintf('%s/**', $indent);
        $lines[] = sprintf('%s * %s', $indent, $this->short);
        $lines[] = $this->blankLine($indent);

        if ($this->long) {
            $lines[] = sprintf('%s * %s', $indent, $this->long);
            $lines[] = $this->blankLine($indent);
        }

        $this->getAnnotations($this->annotations, $lines);

        $lines[] = sprintf('%s */', $indent);

        return implode("\n", $lines);
    }

    protected function getAnnotations(array $annotations, &$lines = [])
    {
        $indent = $this->indent($this->indent);

        foreach ($annotations as $key => $value) {
            if (is_int($key) && null === $value) {
                $lines[] = $this->blankLine($indent);
            } elseif (is_array($value)) {
                $this->getAnnotations($value, $lines);
            } else {
                $lines[] = sprintf('%s * @%s %s', $indent, $key, $value);
            }
        }
    }

    protected function blankLine($indent)
    {
        return sprintf('%s *', $indent);
    }
}
