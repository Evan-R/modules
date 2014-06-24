<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator;

use \Mockery as m;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Validator;

/**
 * @class ValidatorTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $validator = new Validator(m::mock('\Selene\Components\Config\Validator\Nodes\DictNode'));
        $this->assertInstanceof('\Selene\Components\Config\Validator\Validator', $validator);
    }
}
