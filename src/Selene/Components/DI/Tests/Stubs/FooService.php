<?php

namespace Selene\Components\DI\Tests\Stubs;

class FooService
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
