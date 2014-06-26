<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package\Events;

/**
 * @class PublishEvents
 * @package Selene\Components\Package
 * @version $Id$
 */
final class PublishEvents
{
    const EVENT_PUBLISH_PACKAGE = 'package.publish.package';

    const EVENT_PUBLISHED = 'package.publish.file';

    const EVENT_PUBLISH_ERROR = 'package.publish.error';

    const EVENT_PUBLISH_EXCEPTION = 'package.publish.exception';

    const EVENT_NOT_PUBLISHED = 'package.not_publish';

    private function __construct()
    {
    }
}
