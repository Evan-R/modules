<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator\Nodes;

use \Mockery as m;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Nodes\StringNode;
use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;

/**
 * @class DictNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class DictNodeTest extends ArrayNodeTest
{
    public function nodeDefaultValueProvier()
    {
        return [
            [['test' => 'ok']]
        ];
    }

    /**
     * invalidTypeProvider
     *
     * @return array
     */
    public function validTypeProvider()
    {
        return [
            [[]],
            [['foo' => 'bar']]
        ];
    }

    /**
     * invalidTypesProvider
     *
     * @return array
     */
    public function invalidTypesProvider()
    {
        return [
            [[0, 1, 2, 4]],
            ['string']
        ];
    }


    /**
     * {@inheritdoc}
     */
    protected function getNodeClass()
    {
        return 'Selene\Components\Config\Validator\Nodes\DictNode';
    }
}
