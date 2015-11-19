<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer;

/**
 * @interface WriterInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface WriterInterface
{
    /**
     * Adds a line to the line stack.
     *
     * @param string $str
     *
     * @api
     * @return Writer
     */
    public function writeln($str = null);

    /**
     * Inserts a blanc line to the line stack.
     *
     * @api
     * @return Writer
     */
    public function newline();

    /**
     * Concatenates the line stack into a single string.
     *
     * @api
     * @return string
     */
    public function dump();

    /**
     * Remove a line by a given index.
     *
     * @param int $index
     *
     * @api
     * @throws OutOfBoundsException
     *
     * @return Writer
     */
    public function removeln($index = 0);

    /**
     * Replace a line at a given index.
     *
     * @param string $line
     * @param int $index
     *
     * @api
     * @throws OutOfBoundsException
     *
     * @return Writer
     */
    public function replaceln($str, $index = 0);

    /**
     * Removes the last line.
     *
     * @api
     * @return writer
     */
    public function popln();

    /**
     * Adds an indentation to the following line.
     *
     * @api
     * @return Writer
     */
    public function indent();

    /**
     * Removes the previous indentation.
     *
     * @api
     * @return Writer
     */
    public function outdent();

    public function __toString();
}
