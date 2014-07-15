<?php

/**
 * This File is part of the Selene\Components\Kernel\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Events;

/**
 * @class KernelEvents
 * @package Selene\Components\Kernel\Events
 * @version $Id$
 */
class KernelEvents
{
    /**
     * Event dispatched just before the kernel starts to dispatch the request.
     */
    const REQUEST         = 'kernel.request_begin';

    /**
     *
     */
    const END_REQUEST     = 'kernel.request_end';

    /**
     *
     */
    const RESPONSE = 'kernel.response';

    /**
     *
     */
    const FILTER_RESPONSE = 'kernel.filter_response';

    /**
     *
     */
    const HANDLE_RESPONSE  = 'kernel.handle_response';

    /**
     *
     */
    const HANDLE_EXCEPTION = 'kernel.handle_exception';

    /**
     *
     */
    const HANDLE_SHUTDOWN  = 'kernel.handle_shutdown';

    /**
     *
     */
    const ABORT_REQUEST   = 'kernel.abort_request';

    private function __construct()
    {
    }
}
