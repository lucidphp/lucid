<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


use Lucid\Writer\Writer;
use Lucid\Writer\Stringable;
use Lucid\Writer\GeneratorInterface;

/**
 * @class DocBlock implements GeneratorInterface
 *
 * @see GeneratorInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractBlock implements GeneratorInterface
{
    use Stringable;

    /**
     * {@inheritdoc}
     */
    public function generate($raw = self::RV_STRING)
    {
        $writer = new Writer;

        $this->openBlock($writer);
        $this->writeBlock($writer);
        $this->closeBlock($writer);

        return $raw ? $writer : $writer->dump();
    }

    /**
     * isEmpty
     *
     * @return boolean
     */
    abstract public function isEmpty();

    /**
     * openBlock
     *
     * @param Writer $writer
     *
     * @return void
     */
    abstract protected function openBlock(Writer $writer);

    /**
     * closeBlock
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function closeBlock(Writer $writer)
    {
        return $writer->writeln(' */');
    }

    /**
     * writeBlock
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function writeBlock(Writer $writer)
    {
        $newline = false;
        $lnbuff = [];

        $this->doWriteBlock($$writer, $lnbuff, $newline);

        $this->blockLines($writer, $lnbuff);
    }

    abstract protected function doWriteBlock(Writer $writer, array &$lnbuff, &$newline);

    abstract public function copy();

    /**
     * blockLine
     *
     * @param Writer $writer
     * @param array $lines
     *
     * @return void
     */
    protected function blockLines(Writer $writer, array $lines)
    {
        foreach ($lines as $line) {
            $writer->writeln(' * ' . $line);
        }
    }
}
