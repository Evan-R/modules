<?php

/**
 * This File is part of the Selene\Components\Common\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests;

use Selene\Components\Common\IOSerialize;

/**
 * @class IoSerializeTest
 * @package Selene\Components\Common\Tests
 * @version $Id$
 */
class IoSerializeTest extends IOProxyTest
{
    protected function getIO()
    {
        return new IOSerialize();
    }

    /**
     * @dataProvider inDataProvider
     */
    public function testIn($is, $expected)
    {
        parent::testIn($is, serialize($expected));
    }


    /**
     * @dataProvider outDataProvider
     */
    public function testOut($is, $expected)
    {
        parent::testOut(serialize($is), $expected);
    }
}
