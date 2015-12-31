<?php

namespace Acme;

use Acme\Traits\BarTrait;
use Acme\Traits\FooTrait;

/**
 * @class Foo
 * @see Acme\Bar
 * @see Acme\Baz
 */
class Foo extends Bar implements Baz
{
    use FooTrait,
        BarTrait {
        FooTrait::bar as private baz;
        BarTrait::foo insteadof FooTrait;
    }
}
