<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\File;

use Lucid\Writer\Writer;
use Lucid\Writer\GeneratorInterface;

/**
 * @class PhpGenerator
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpGenerator implements GeneratorInterface
{
    /** @var string */
    private $contents;

    /**
     * Constructor.
     *
     * @param bool $raw
     */
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
