<?php

require __DIR__ . '/vendor/autoload.php';

use Lucid\Signal\Event;
use Lucid\Signal\EventName;

class Foo
{
    private $bar;
    public function __construct()
    {
        $this->bar = new Bar($this);

    }

    public function getBar() {
        return $this->bar;
    }
}

class Bar
{
    private $foo;
    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        $bar = $this->foo->getBar();
        if ($this === $bar) {
            return null;
        }

        return $this->foo;
    }
}

$foo = new Foo();
$bar = new Bar($foo);
var_dump($bar->getFoo());
