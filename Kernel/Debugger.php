<?php

/**
 * This File is part of the Selene\Components\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel;

/**
 * @class Debugger
 * @package Selene\Components\Kernel
 * @version $Id$
 */
class Debugger
{
    private $stopTime;

    private $startTime;

    private $started;

    /**
     * start
     *
     *
     * @access public
     * @return void
     */
    public function start()
    {
        $this->started = true;
        $this->startTime = microtime(true);
    }

    /**
     * stop
     *
     *
     * @access public
     * @return void
     */
    public function stop()
    {
        $this->started = false;
        $this->stopTime = microtime(true);
    }

    /**
     * getRuntime
     *
     * @param boolean $asfloat
     *
     * @access public
     * @return float|string
     */
    public function getRuntime($asFloat = true)
    {
        if ($this->started) {
            $this->stop();
        }
        $time = $this->stopTime - $this->startTime;
        return $asFloat ? $time : sprintf('%f', $time);
    }
}
