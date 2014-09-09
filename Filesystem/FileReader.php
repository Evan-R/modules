<?php

/*
 * This File is part of the Selene\Module\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem;

/**
 * @class FileReader
 * @package Selene\Module\Filesystem
 * @version $Id$
 */
class FileReader
{
    /**
     * Constructor.
     *
     * @param string $file
     * @param int $buffsize
     *
     */
    public function __construct($file, $buffsize = 1024)
    {
        $this->file = $file;
        $this->buffsize = $buffsize;
    }

    /**
     * read
     *
     * @param mixed $startOffset
     * @param mixed $endOffset
     * @param callable $callback
     *
     * @access public
     * @return void
     */
    public function read(callable $callback = null, $max = -1)
    {
        $reg    = new \SplFixedArray(2);
        $handle = fopen($this->file, 'r');

        $res = [];
        $i = 0;

        try {
            do {
                $this->readln($handle, $reg, $this->buffsize, $callback, $i);
                $res[] = $reg[0];
            } while ($this->isValid($handle, $reg[1], $i, $max));
        } catch (\Exception $e) {
        }

        fclose($handle);

        return implode("\n", $res);
    }

    /**
     * readln
     *
     * @param mixed $handle
     * @param mixed $buffsize
     * @param callable $callback
     *
     * @access protected
     * @return string
     */
    protected function readln($handle, \SplFixedArray &$reg, $buffsize, callable $callback = null, &$i = 0)
    {
        $reg[0] = stream_get_line($handle, $buffsize, "\n");

        if (null !== $callback) {
            $reg[1] = call_user_func($callback, $reg[0], $i++);
        }
    }

    /**
     * isValid
     *
     * @param resource $handle
     * @param mixed $result
     *
     * @return boolean
     */
    protected function isValid($handle, $result = null, $line = 0, $max = -1)
    {
        if (feof($handle) || null !== $result || ($max > 0 && $max < $line + 1)) {
            return false;
        }

        return true;
    }
}
