<?php

namespace Selene\Components\DependencyInjection\Tests\Stubs;

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
