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

use \Selene\Components\Events\Event;
use \Selene\Components\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteFilterAfterEvent
 * @package Selene\Components\Routing\Events
 * @version $Id$
 */
class RouteFilterAfterEvent extends RouteFilterEvent
{
    private $response;

    public function __construct(RouteDispatchEvent $dispatched)
    {
        parent::__construct($dispatched->getRoute(), $dispatched->getRequest());
        $this->dispatched = $dispatched;
    }

    public function setResponse($response)
    {
        $this->dispatched->setResponse($response);
    }

    /**
     * getResponse
     *
     * @access public
     * @return mixed
     */
    public function getResponse()
    {
        $this->dispatched->getResponse();
    }
}
