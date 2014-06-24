<?php

/**
 * This File is part of the Selene\Components\Http package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Http;

use \Symfony\Compoment\HttpFoundation\ParameterBag;
use \Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @class Request extends SymfonyRequest
 * @see SymfonyRequest
 *
 * @package Selene\Components\Http
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Request extends SymfonyRequest
{
    /**
     * getFromQuery
     *
     * @param mixed $param
     * @param mixed $default
     * @param mixed $deep
     *
     * @access public
     * @return mixed
     */
    public function getFromQuery($param, $default = null, $deep = false)
    {
        return $this->query->get($param, $default, $deep);
    }

    /**
     * getFromRequest
     *
     * @param mixed $param
     * @param mixed $default
     * @param mixed $deep
     *
     * @access public
     * @return mixed
     */
    public function getFromRequest($param, $default = null, $deep = false)
    {
        return $this->request->get($param, $default, $deep);
    }

    /**
     * getAttribute
     *
     * @param mixed $attr
     * @param mixed $default
     * @param mixed $deep
     *
     * @access public
     * @return mixed
     */
    public function getAttribute($attr, $default = null, $deep = false)
    {
        return $this->attributes->get($attr, $default, $deep);
    }

    /**
     * getInput
     *
     * @access public
     * @return array
     */
    public function getInput()
    {
        return new ParameterBag(array_merge($this->attributes->all(), $this->query->all(), $this->request->all()));
    }

    /**
     * isAjax
     *
     * @access public
     * @return boolean
     */
    public function isAjax()
    {
        return $this->isXmlHttpRequest();
    }
}
