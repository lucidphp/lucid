<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

/**
 * @class Section
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Section
{
    /**
     * Interal content buffer.
     *
     * @var array array of strings.
     */
    private $cbuff = [];

    /**
     * Start section buffer.
     *
     * @return void
     */
    public function start()
    {
        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Stop section buffer.
     *
     * @return void
     */
    public function stop()
    {
        $this->cbuff[] = ob_get_clean();
    }

    /**
     * Reset the contents buffer;
     *
     * @return void
     */
    public function reset()
    {
        $this->cbuff = [];
    }

    /**
     * Get the contents.
     *
     * @param int|null $index
     * @param boolean $raw
     *
     * @throws \OutOfBoundsException if the given indenx is invalid.
     * @return string|array
     */
    public function getContent($index = null, $raw = false)
    {
        if (null === $index) {
            return (bool)$raw ? $this->cbuff : join('', $this->cbuff);
        }

        if (isset($this->cbuff[(int)$index])) {
            return $this->cbuff[(int)$index];
        }

        throw new \OutOfBoundsException(sprintf('No content at buffer %d or empty buffer.', (int)$index));
    }
}
