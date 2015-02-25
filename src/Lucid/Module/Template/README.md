# Templating library

![Build Status](https://img.shields.io/travis/iwyg/template.svg?style=flat-square)
![Coverage](https://img.shields.io/coveralls/iwyg/template.svg?style=flat-square)

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



## Partials

### Inserts

```php
<html>
    <body>

    <div id="container">
        $view->insert('partials/footer.php');
        $view->insert('partials/content.php');
        $view->insert('partials/footer.php');
    </div>

    </body>
</html>
```

### Extending existing templates

The templates

`partials/content.php`:

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
        <p>The default content.</p>
      <?= $view->endsection() ?>
    </div>
  </body>
</html>
```

### Sections
