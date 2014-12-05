# Templating library

mofo

## Installation

## Getting started

There is something to show here.


A new episode is comming.

```php
<?php

use Lucid\Module\Template\Engine;
use Lucid\Module\Template\Loader\FilesystemLoader;

$engine = new Engine(new Loader(['path/to/templates']));

$engine->render('customer/greeting.php', ['hello' => $customer]);

```
