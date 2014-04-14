<?php

/**
 * This File is part of the Selene\Components\Net package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Http;

use \SplStack;
use \Countable;
use \Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @class RequestStack implements StackInterface
 * @see StackInterface
 *
 * @package Selene\Components\Net
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RequestStack implements StackInterface, Countable
{
    /**
     * @access public
     */
    public function __construct()
    {
        $this->stack = new SplStack;
    }

    /**
     * getCurrent
     *
     * @access public
     * @return mixed
     */
    public function getCurrent()
    {
        return $this->isEmpty() ? null : $this->stack->top();
    }

    /**
     * Return the previous request from the stack.
     *
     * If not previous request is available,
     * the current request is returned.
     *
     * @access public
     * @return null|Request
     */
    public function getPrevious()
    {
        if (0 === ($count = $this->stack->count()) || $count < 2) {
            return null;
        }
        return $this->stack[$count - ($count - 1)];
    }

    /**
     *
     * @access public
     * @return mixed
     */
    public function count()
    {
        return $this->stack->count();
    }

    /**
     * removeLast
     *
     * @access public
     * @return mixed
     */
    public function removeLast()
    {
        return $this->pop();
    }

    /**
     * isEmpty
     *
     * @access public
     * @return mixed
     */
    public function isEmpty()
    {
        return 0 === $this->stack->count();
    }

    /**
     * removeAll
     *
     * @access public
     * @return mixed
     */
    public function removeAll()
    {
        // stack->valid won't work strangely
        while ($this->count()) {
            $this->stack->pop();
        }
    }

    /**
     * push
     *
     * @param Request $request
     *
     * @access public
     * @return mixed
     */
    public function push(SymfonyRequest $request)
    {
        return $this->stack->push($request);
    }

    /**
     * pop
     *
     * @access public
     * @return mixed
     */
    public function pop()
    {
        return $this->stack->pop();
    }

    /**
     * removeAllButFirst
     *
     * @access public
     * @return mixed
     */
    public function removeSubRequests()
    {
        $req = $this->stack->bottom();
        $this->removeAll();
        $this->stack->push($req);
    }

    /**
     * getMain
     *
     * @access public
     * @return Request|null
     */
    public function getMain()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->stack->bottom();
    }
}
