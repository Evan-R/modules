<?php

/**
 * This File is part of the Stream\Cryptography package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cryptography\Tests;

use Selene\Module\TestSuite\TestCase;
use Selene\Module\Cryptography\HashKey;

/**
 * Class HashKeyTest
 * @author
 */
class HashKeyTest extends HashTestCase
{

    protected function setUp()
    {
        $this->hash = new HashKey('9asdz47aazrasda72haHgGadh3hasgdh32OO8');
    }
}
