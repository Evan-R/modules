<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Traits;

/**
 * Class: SegmentParser
 *
 * @abstract
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait SegmentParser
{
    /**
     * parsed
     *
     * @var array
     */
    private $parsed = [];

    /**
     * parsedSegments
     *
     * @var array
     */
    private $parsedSegments = [];

    /**
     * segmentSeparator
     *
     * @var string
     */
    private static $segmentSeparator = '.';

    /**
     * namespaceSeparator
     *
     * @var string
     */
    private static $namespaceSeparator = '::';

    /**
     * parse
     *
     * @param mixed $descriptor
     * @access public
     * @return mixed
     */
    public function parseSegment($descriptor)
    {
        if (!isset($this->parsed[$descriptor])) {
            if (false !== strpos($descriptor, static::$namespaceSeparator)) {
                $parsed = $this->parseNamespace($descriptor);
            } else {
                $parsed = $this->parseSegmentString($descriptor);
            }
            $this->parsed[$descriptor] = $parsed;
        }
        return $this->parsed[$descriptor];
    }

    /**
     * parseSegment
     *
     * @param mixed $descriptor
     * @access protected
     * @return mixed
     */
    private function parseSegmentString($descriptor)
    {
        if (isset($this->parsedSegments[$descriptor])) {
            return $this->parsedSegments[$descriptor];
        }

        $parts = explode(static::$segmentSeparator, $descriptor);
        $item = null;

        if (count($parts) > 1) {
            list($segment, $item) = $parts;
        } else {
            $segment = current($parts);
        }

        $this->parsedSegments[$descriptor] = [null, $segment, $item];
        return $this->parsedSegments[$descriptor];
    }

    /**
     * parseNamespace
     *
     * @param mixed $descriptor
     *
     * @access private
     * @return array
     */
    private function parseNamespace($descriptor)
    {
        $parts = explode(static::$namespaceSeparator, $descriptor);
        $namespace = array_shift($parts);

        $temp = $this->parseSegment(array_shift($parts));
        array_shift($temp);
        array_unshift($temp, $namespace);
        $temp;
        return $temp;
    }
}
