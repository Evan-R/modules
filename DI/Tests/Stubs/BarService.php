<?php

namespace Selene\Module\DI\Tests\Stubs;

class BarService
{
    private $foo;

    public function __construct(FooService $foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}
