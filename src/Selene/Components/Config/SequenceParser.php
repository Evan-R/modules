<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config;

/**
 * @class SequenceParser
 * @package
 * @version $Id$
 */
class SequenceParser
{
    /**
     * parsed
     *
     * @var array
     */
    protected $parsed = [];

    /**
     * parsedSegments
     *
     * @var array
     */
    protected $parsedSegments = [];

    /**
     * segmentSeparator
     *
     * @var string
     */
    protected static $segmentSeparator = '.';

    /**
     * namespaceSeparator
     *
     * @var string
     */
    protected static $namespaceSeparator = '::';

    /**
     * parse
     *
     * @param mixed $descriptor
     * @access public
     * @return array
     */
    public function parseSequence($descriptor)
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

        list($group, $items) = array_pad($this->splitSequence($descriptor), 2, null);

        $this->parsedSegments[$descriptor] = [null, $group, $items];

        return $this->parsedSegments[$descriptor];
    }

    /**
     * parseNamespace
     *
     * @param mixed $descriptor
     *
     * @access protected
     * @return mixed
     */
    protected function parseNamespace($descriptor)
    {
        list($namespace, $sequence) = $this->splitNamespace($descriptor);
        return $this->parseNamespaceParts($namespace, $sequence);
    }

    /**
     * parseNamespaceParts
     *
     * @param mixed $namespace
     * @param mixed $sequence
     *
     * @access protected
     * @return array
     */
    protected function parseNamespaceParts($namespace, $sequence)
    {
        $items = array_pad($this->parseSegmentString($sequence), 1, null);
        return array_merge((array)$namespace, array_splice($items, 1));
    }


    /**
     * splitSequence
     *
     * @param mixed $descriptor
     *
     * @access protected
     * @return array
     */
    protected function splitSequence($descriptor)
    {
        return preg_split('#'.preg_quote(static::$segmentSeparator).'#', $descriptor, 2, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * splitNamespace
     *
     * @param mixed $descriptor
     *
     * @access protected
     * @return array
     */
    protected function splitNamespace($descriptor)
    {
        return explode(static::$namespaceSeparator, $descriptor);
    }

    /**
     * getNsSeparator
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getNsSeparator()
    {
        return static::$namespaceSeparator;
    }

    /**
     * getSequenceSeparator
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getSequenceSeparator()
    {
        return static::$segmentSeparator;
    }
}
