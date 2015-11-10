<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests;

use Lucid\Template\View;
use Lucid\Template\Engine;
use Lucid\Template\Loader\FilesystemLoader;

/**
 * @class RenderTest
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldRenderData()
    {
        $engine = $this->newEngine();
        $expected = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>Hello</title>
  </head>
  <body>
  </body>
</html>
EOF;
        $this->assertXmlStringEqualsXmlString($expected, $engine->render('index.php', ['title' => 'Hello']));
    }

    /** @test */
    public function itShouldRenderInserts()
    {
        $engine = $this->newEngine();
        $expected = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>title</title>
  </head>
  <body>
    <div id="main"></div>
  </body>
</html>
EOF;
        $this->assertXmlStringEqualsXmlString($expected, $engine->render('include.0.php'));
    }

    /** @test */
    public function itShouldRenderInsertsWithData()
    {
        $engine = $this->newEngine();
        $expected = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>title</title>
  </head>
  <body>
    <div id="main">Hello World!</div>
  </body>
</html>
EOF;
        $this->assertXmlStringEqualsXmlString($expected, $engine->render('include.1.php'));
    }

    /** @test */
    public function itIsShouldExtendTemplates()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));
        $expected = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>title</title>
  </head>
  <body>
    <div>some content</div>
  </body>
</html>
EOF;
        $this->assertXmlStringEqualsXmlString($expected, $engine->render('partials/extend.0.php'));
    }

    protected function newEngine()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view'));

        return $engine;
    }
}
