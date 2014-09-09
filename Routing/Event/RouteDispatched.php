<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Event;

use \Selene\Module\Routing\Matchers\MatchContext;

/**
 * @class RouteDispatchEvent
 * @package Selene\Module\Routing\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RouteDispatched extends RouteEvent
{
    /**
     * context
     *
     * @var \Selene\Module\Routing\Matchers\MatchContext
     */
    private $context;

    /**
     * response
     *
     * @var mixed
     */
    private $response;

    /**
     * Contructor.
     *
     * @param MatchContext $context
     */
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
     * Set the response
     *
     * @param mixed $response
     *
     * @return void
     */
    public function setResponse($response)
    {
        $this->stopPropagation();
        $this->response = $response;
    }

    /**
     * Get the response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
