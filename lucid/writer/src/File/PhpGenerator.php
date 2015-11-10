<?php

/*
 * This File is part of the Selene\Module\Writer\Generator\File package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\File;

use \Selene\Module\Writer\Writer;
use \Selene\Module\Writer\GeneratorInterface;

/**
 * @class PhpGenerator
 * @package Selene\Module\Writer\Generator\File
 * @version $Id$
 */
class PhpGenerator implements GeneratorInterface
{
    private $contents;

    public function generate($raw = false)
    {
        $writer = new Writer;
        $writer
            ->writeln('<?php')
            ->newline();

        if (null != $this->contents) {
            $writer
                ->writeln(rtrim($this->contents, PHP_EOL));
        }

        $writer->appendln(PHP_EOL);

        return $raw ? $writer : $writer->dump();
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
    }
}
