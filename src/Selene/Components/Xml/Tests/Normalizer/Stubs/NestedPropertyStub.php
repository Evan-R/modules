<?php

/**
 * This File is part of the Selene\Components\Xml\Tests\Normalizer\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Tests\Normalizer\Stubs;

/**
 * @class NestedPropertyStub
 * @package Selene\Components\Xml\Tests\Normalizer\Stubs
 * @version $Id$
 */
class NestedPropertyStub
{
    public $baz;
    public function __construct()
    {
        $this->baz = new ConvertToArrayStub;
    }
}
