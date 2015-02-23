# Templating library

An extendable templating library for php.

## Installation

```json
{
	"require": {
		"lucid/template":"dev-master"
	}
}
```

```bash
$ composer install
```

## Getting started

```php
<?php

use Lucid\Module\Template\Engine;
use Lucid\Module\Template\Loader\FilesystemLoader;

$engine = new Engine(new Loader(['path/to/templates']));

$engine->render('partials/content.php', ['title' => 'Hello World!']);

```

The templates

`partial/contents.php`:

```php

<?= $view->extend('master.php') ?>
    
<?= $view->section('content') ?>
    <p>Extended content</p>
<?= $view->endsection() ?>

```


`master.php`:

```php
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?= $title ?></title>
    </head>
    <body>
    <div id="main">
        <?= $view->section('content') ?>
        <?= $view->endsection() ?>
    </div>
    </body>
</html>
```
