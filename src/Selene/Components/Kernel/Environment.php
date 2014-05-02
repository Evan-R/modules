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
 * @class Environment
 * @package Selene\Components\Kernel
 * @version $Id$
 */
class Environment
{
    protected $env;

    protected $host;

    protected $environments;

    /**
     * @param array $environments
     * @param array $server
     *
     * @access public
     */
    public function __construct(array $environments, array $server = null)
    {
        $this->environments = $environments;
        $this->getHost($server ?: $_SERVER);
    }


    /**
     * runningInConsole
     *
     * @access public
     * @return boolean
     */
    public function runningInConsole()
    {
        return 'cli' == php_sapi_name();
    }

    /**
     * detect
     *
     * @access public
     * @return string
     */
    public function detect()
    {
        return 'dev';
    }

    /**
     * getHost
     *
     * @param array $server
     *
     * @access protected
     * @return void
     */
    protected function getHost(array $server)
    {
        return isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : 'localhost';
    }
}
