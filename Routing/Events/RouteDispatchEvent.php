<?php

/**
 * This File is part of the Selene\Components\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Events;

use \Selene\Components\Routing\Matchers\MatchContext;

/**
 * @class RouteDispatchEvent
 * @package Selene\Components\Routing\Events
 * @version $Id$
 */
class RouteDispatchEvent extends RouteEvent
{
    private $response;

    private $context;

    public function __construct(MatchContext $context)
    {
        $this->context = $context;

        parent::__construct($context->getRoute(), $context->getRequest());
    }

    /**
     * getContext
     *
     *
     * @return MatchContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * setResponse
     *
     * @param mixed $response
     *
     * @access public
     * @return mixed
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * getResponse
     *
     *
     * @access public
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
