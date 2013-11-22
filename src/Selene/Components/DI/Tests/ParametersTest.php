<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\DI\Parameters;

/**
 * @class ParametersTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\DI\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ParametersTest extends TestCase
{

    protected $params;

    protected function setUp()
    {
        $this->params = new Parameters;
    }

    /**
     * @test
     */
    public function testReplaceString()
    {
        $this->params->set('%replace%', 'some string');
        $this->params->get('%replace%');

        $this->assertSame('some string got replaced here', $this->params->replaceString('%replace% got replaced here'));
    }

    /**
     * @test
     */
    public function testSetKey()
    {
        $this->params->set('%key%', 'value');

        $this->assertTrue($this->params->has('%key%'));

        $this->assertFalse($this->params->has('$key$'));
        $this->assertFalse($this->params->has('@key@'));
    }

    /**
     * @test
     */
    public function testGetKey()
    {
        $this->params->set('%key%', 'value');
        $this->assertSame('value', $this->params->get('%key%'));
    }
}
