<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

/**
 * @trait ImportHelper
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait ImportHelper
{
    /** @var ImportResolver */
    private $importResolver;

    /**
     * Returns the ImportResolver instance.
     *
     * @return ImportResolver
     */
    public function getImportResolver()
    {
        if (null === $this->importResolver) {
            $this->importResolver = new ImportResolver;
        }

        return $this->importResolver;
    }

    /**
     * Adds an import to a given pool.
     *
     * @param array $pool the pool passed by reference.
     * @param string $string the import name.
     *
     * @return void
     */
    protected function addToImportPool(array &$pool, $string)
    {
        $this->getImportResolver()->add($string);
        $pool[] = $string;
    }
}
