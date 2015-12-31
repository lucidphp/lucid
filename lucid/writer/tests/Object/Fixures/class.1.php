<?php

namespace Acme;

use Acme\Interfaces\Bar as FooBar;
use Acme\Lib\Bar;

/**
 * @class Foo
 * @see Acme\Lib\Bar
 * @see FooBar
 */
class Foo extends Bar implements FooBar
{
}
