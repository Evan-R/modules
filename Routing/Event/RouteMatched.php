<?php

/*
 * This File is part of the Selene\Module\Routing\Event package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Event;

use \Selene\Module\Events\Event;
use \Selene\Module\Routing\Matchers\MatchContext;

/**
 * @class RouteMatched
 *
 * @package Selene\Module\Routing\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteMatched extends Event
{
    /**
     * Constructor.
     *
     * @param MatchContext $context
     */
    public function __construct(MatchContext $context)
    {
        $this->context = $context;
    }

    /**
     * getContext
     *
     * @return MatchContext
     */
    public function getContext()
    {
        return $this->context;
    }
}
