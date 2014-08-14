<?php

/**
 * This File is part of the Selene\Module\Filesystem\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Filter;

/**
 * @class FilterInterface
 * @package
 * @version $Id$
 */
interface FilterInterface
{
    public function match($pattern);

    public function not($pattern);
}
