<?php

/**
 * This File is part of the Selene\Components\Filesystem\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Filter;

/**
 * @class FileFilter
 * @package
 * @version $Id$
 */
class FileFilter extends AbstractFilter
{
    protected $filter;

    protected $compiled;

    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }

    protected function compile()
    {
        $this->compiled = sprintf('~(%s)~', implode('|', $this->filter));
    }

    /**
     * isRegexp
     *
     * @param mixed $pattern
     *
     * @access protected
     * @return bool
     */
    protected function isRegexp($pattern)
    {
        return preg_match('#^[^a-zA-Z0-9]#', $pattern) and (substr($pattern, 0, 1) === substr($pattern, -1));
    }

    /**
     * removeModifyer
     *
     * @param mixed $pattern
     *
     * @access protected
     * @return bool
     */
    protected function removeModifyer($pattern)
    {
        return preg_replace('~([^a-zA-Z0-9])[imxe{4}]$~', '$1', $pattern);
    }

    /**
     * doMatch
     *
     * @param string $pattern
     *
     * @access protected
     * @return bool
     */
    protected function doMatch($pattern)
    {
        return (bool)preg_match($this->compiled, $pattern);
    }
}
