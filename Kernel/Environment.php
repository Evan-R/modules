<?php

/**
 * This File is part of the Selene\Module\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel;

/**
 * @class Environment
 *
 * @package Selene\Module\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Environment
{
    /**
     * env
     *
     * @var string
     */
    protected $env;

    /**
     * host
     *
     * @var string
     */
    protected $host;

    /**
     * environments
     *
     * @var mixed
     */
    protected $environments;

    /**
     * cliArguments
     *
     * @var array
     */
    protected $cliArguments;

    /**
     * @param array $environments
     * @param array $server
     *
     * @access public
     */
    public function __construct($environments, array $server = null, array $consoleArgs = null)
    {
        $this->environments = $environments;
        $this->getHost($server ?: $_SERVER);
        $this->cliArguments = $consoleArgs ?: isset($_SERVER['argv']) ? $_SERVER['argv'] : [];
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
        if (isset($this->env)) {
            return $this->env;
        }
        if (is_callable($this->environments)) {
            return $this->env = call_user_func($this->environments);
        }

        if ($this->runningInConsole()) {
            return $this->env = $this->detectCliEnv();
        }

        return $this->env = $this->detectWebEnv();
    }

    /**
     * detectWebEnv
     *
     * @access protected
     * @return string
     */
    protected function detectWebEnv()
    {
        $environment = 'production';

        foreach ($this->environments as $env => $vars) {
            foreach ($vars as $var) {
                if ($this->hostIsMachine($var)) {
                    $environment = $env;
                } elseif ($this->hostIsHost($var)) {
                    $environment = $env;
                }
            }
        }

        return $environment;
    }

    /**
     * detectCliEnv
     *
     * @access protected
     * @return string
     */
    protected function detectCliEnv()
    {
        foreach ($this->cliArguments as $arg) {
            if (0 === strpos($arg, '--env')) {
                return substr($arg, 1 + strpos($arg, '='));
            }
        }

        return $this->detectWebEnv();
    }

    /**
     * hostIsMachine
     *
     * @param mixed $host
     *
     * @access public
     * @return boolean
     */
    protected function hostIsMachine($host)
    {
        return $this->matchString(gethostname(), $host);
    }

    /**
     * hostIsHost
     *
     * @param mixed $host
     *
     * @access public
     * @return boolean
     */
    protected function hostIsHost($host)
    {
        return $this->matchString($this->host, $host);
    }

    /**
     * matchString
     *
     * @param mixed $string
     * @param mixed $pattern
     *
     * @access protected
     * @return boolean
     */
    protected function matchString($string, $pattern)
    {
        if (0 === strcmp($string, $pattern)) {
            return true;
        }

        return (bool)preg_match('#'. str_replace('\*', '.*', preg_quote($pattern, '#')).'\z#', $string);
    }

    /**
     * getHost
     *
     * @param array $server
     *
     * @access protected
     * @return string
     */
    protected function getHost(array $server)
    {
        if (!$this->host) {
            $this->host = isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : 'localhost';
        }

        return $this->host;
    }
}
