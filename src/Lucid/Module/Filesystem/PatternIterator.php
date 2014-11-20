<?php

/*
 * This File is part of the Lucid\Module\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem;

/**
 * @class PatternIterator
 * @package Selene\Module\Filesystem
 * @version $Id$
 */
class PatternIterator extends \RegexIterator
{
    /**
     * Constructor.
     *
     * @param string $path
     * @param string $pattern
     * @param int    $depth
     * @param int    $flags
     * @param int    $dirFlags
     */
    public function __construct($path, $pattern, $depth = null, $flags = self::MATCH, $dirFlags = null)
    {
        parent::__construct($this->createInnerIterator($path, $depth, $dirFlags), $pattern, $flags);
    }

    /**
     * setMaxDepth
     *
     * @param int $depth
     *
     * @return void
     */
    public function setMaxDepth($depth = -1)
    {
        $this->getInnerIterator->setMaxDept((int)$depth);
    }

    /**
     * getMaxDepth
     *
     *
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->getInnerIterator->getMaxDept();
    }

    /**
     * getRecursiveIterator
     *
     * @param string $path
     * @param int    $depth
     * @param int    $flags
     *
     * @return \RecursiveIteratorIterator
     */
    private function createInnerIterator($path, $depth = null, $flags = null)
    {
        //$rcit = new \RecursiveIteratorIterator(
            //new \RecursiveDirectoryIterator($path, $flags)
        //);
        $rcit = new \RecursiveIteratorIterator(new \RecursiveIterator);


        if (0 > (int)$depth) {
            $rcit->setMaxDepth($depth);
        }

        return $rcit;
    }
}
