<?php

/**
 * This File is part of the Selene\Module\Common\Tests\Stubs\Serializable package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Stubs\Serializable;

use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\Common\Traits\SerializeableJson;

/**
 * @class JsonSerializableStub
 * @package Selene\Module\Common\Tests\Stubs\Serializable
 * @version $Id$
 */
class JsonSerializableStub
{
    use Getter;
    use SerializeableJson;

    private $attrs = [
        'foo' => 'bar'
    ];

    private $pbar = 'private';

    protected $ppbar = 'protected';

    public $array = ['public'];

    public $string = 'public';

    public function __get($key)
    {
        return $this->defaultGet($this->attrs, $key, null);
    }
}
