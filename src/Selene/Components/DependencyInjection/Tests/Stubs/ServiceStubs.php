<?php


class BarService
{
    private $foo;

    public function __construct(FooService $foo)
    {
        $this->foo = $foo;
    }
}

class BazService extends BarService
{

}

class BlaService extends BarService
{
    public function __construct(FooService $foo, $bam = null)
    {
        parent::__construct($foo);
        $this->bam = $bam;
    }
    public function setBoom($boom)
    {
        $this->boom = $boom;
    }
    public function setBaam($baam)
    {
        $this->baam = $baam;
    }
}
