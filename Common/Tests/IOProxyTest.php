<?php

/**
 * This File is part of the Selene\Module\Common\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests;

use Selene\Module\TestSuite\TestCase;
use Selene\Module\Common\IOPassThrough;

/**
 * @class IOProxyTest
 * @package Selene\Module\Common\Tests
 * @version $Id$
 */
class IOProxyTest extends TestCase
{
    protected $io;

    protected function setUp()
    {
        $this->io = $this->getIO();
    }

    protected function getIO()
    {
        return new IOPassThrough();
    }

    /**
     * @dataProvider inDataProvider
     */
    public function testIn($is, $expected)
    {
        $this->assertSame($expected, $this->io->in($is));
    }


    /**
     * @dataProvider outDataProvider
     */
    public function testOut($is, $expected)
    {
        $this->assertSame($expected, $this->io->out($is));
    }

    public function inDataProvider()
    {
        return [
            ['string', 'string'],
            [[1,2], [1,2]],
        ];
    }

    public function outDataProvider()
    {
        return [
            ['string', 'string'],
            [[1,2], [1,2]],
        ];
    }
}
