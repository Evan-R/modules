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

    private $filterBase;

    protected $filter;

    protected $compiled;

    public function __construct(array $filter, $filterBase = '/')
    {
        $this->filter     = $filter;
        $this->filterBase = $filterBase;
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
        //var_dump($pattern, $this->compiled);
        return (bool)preg_match($this->compiled, $pattern);
    }
}
