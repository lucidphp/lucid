# Templating library

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/signal-blue.svg?style=flat-square)](https://github.com/lucidphp/template/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/template/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/template/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/template)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/template/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/template)
[![HHVM](https://img.shields.io/hhvm/lucid/template/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/template)

An extendable templating library for php.

## Requirements

```
php >= 5.6
```

## Installation
```bash
$ composer require lucid/template
```

## Getting started

```php
<?php

use Lucid\Template\Engine;
use Lucid\Template\Loader\FilesystemLoader;

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

### Tempalte Listeners

Adding template listeners can be usefull if you want to add data to a specific
template. This data my be derieved from any resource you may want (e.g. DB,
Container, etc).

```php
<?php

$view->addListener('head.php', new RenderHeadListener($headerData));
```

Your listener may look something like this

```php
<?php

use Lucid\Template\Listener\ListenerInterface;

class RenderHeadListener implements ListenerInterface
{
	private $data;

	public function __construct(array $headerData)
	{
		$this->data = $data;
	}

    public function onRender(TemplateDataInterface $data)
	{
		// add header data to `$data`
	}
}
```
