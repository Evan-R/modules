<?php

namespace Selene\Components\DependencyInjection\Tests\Stubs;

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
