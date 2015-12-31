<?php

namespace Acme\Traits;

use Acme\Test\HelperTrait;

/**
 * @trait FooTrait
 */
trait FooTrait
{
    use BarTrait,
        HelperTrait {
        BarTrait::getFoo as public bla;
        BarTrait::retrieve insteadof HelperTrait;
    }

    /** @var mixed */
    public $foo;
}
