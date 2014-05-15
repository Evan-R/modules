<?php

/**
 * This File is part of the Selene\Components\DI\Dumper\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Traits;

/**
 * @class FormatterTrait
 * @package Selene\Components\DI\Dumper\Traits
 * @version $Id$
 */
trait FormatterTrait
{
    /**
     * indent
     *
     * @param int $count
     *
     * @access public
     * @return string
     */
    public function indent($count = 4)
    {
        return $count <= 0 ? '' : str_repeat(' ', (int)$count);
    }

    /**
     * extractParams
     *
     * @param array $params
     * @param int $indent
     *
     * @access protected
     * @return string
     */
    public function extractParams(array $params, $indent = 12)
    {
        $array = [];

        foreach ($params as $param => $value) {

            if (is_array($value)) {
                $value = $this->extractParams($value, $indent + 4);
            } elseif (is_string($value) && 0 === strpos($value, '$this')) {
                $value = $value;
            } else {
                $value = $this->exportVar($value);
            }

            $array[] = sprintf('%s%s => %s,', $this->indent($indent), $this->exportVar($param), $value);

        }

        return empty($array) ?
            '[]' :
            preg_replace(
                '#\d+ \=\>\s?#i',
                '',
                sprintf("[\n%s\n%s]", implode("\n", $array), $this->indent($indent - 4))
            );
    }

    /**
     * dumpExport
     *
     * @param mixed $param
     *
     * @access public
     * @return string
     */
    public function exportVar($param)
    {
        return preg_replace('~NULL~', 'null', var_export($param, true));
    }
}
