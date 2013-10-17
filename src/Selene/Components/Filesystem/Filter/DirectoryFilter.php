<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Filter;

/**
 * @class DirectoryFilter
 * @see Filter
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class DirectoryFilter extends AbstractFilter
{

    /**
     * filterBase
     *
     * @var string
     */
    private $filterBase;

    /**
     * filter
     *
     * @var array
     */
    protected $filter;

    /**
     * compiled
     *
     * @var string
     */
    protected $compiled;

    /**
     * __construct
     *
     * @param array $filter
     * @param string $filterBase
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $filter, $filterBase = '/')
    {
        $this->filter     = $filter;
        $this->filterBase = $filterBase;
    }

    /**
     * add
     *
     * @param mixed $expression
     *
     * @access public
     * @return void
     */
    public function add($expression)
    {
        $this->filter = array_merge($this->filter, $filter);
    }

    /**
     * compile
     * @access public
     * @return string
     */
    protected function compile()
    {
        if (!is_array($this->compiled)) {
            $directories =
                ($path = '^'.$this->filterBase.DIRECTORY_SEPARATOR)
                .implode('|'.$path, str_replace('\\\//', DIRECTORY_SEPARATOR, $this->filter));

            $this->compiled = sprintf('~(%s).*?~', $directories);
        }

        return;
    }

    /**
     * doMatch
     *
     * @param mixed $pattern
     *
     * @access protected
     * @return mixed
     */
    protected function doMatch($pattern)
    {
        return (bool)preg_match($this->compiled, str_replace('\\', '/', $pattern));
    }
}
