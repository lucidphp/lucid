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
use Lucid\Writer\Stringable;
use Lucid\Writer\FormatterHelper;
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
    use FormatterHelper,
        Stringable;

    /** @var array */
    private $contents = [];

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

        array_map([$writer, 'writeln'], $this->contents);

        $writer->appendln(PHP_EOL);

        return $raw ? $writer : $writer->dump();
    }

    /**
     * addString
     *
     * @param string $string
     *
     * @return void
     */
    public function addString($string)
    {
        $this->contents[] = $string;
    }

    /**
     * addArray
     *
     * @param array $array
     *
     * @return void
     */
    public function addArray(array $array)
    {
        $this->contents  = $this->contents + explode("\n", $this->extractParams($array));
    }
}
