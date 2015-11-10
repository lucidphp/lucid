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
    /**
     * importResolver
     *
     * @var ImportResolver
     */
    private $importResolver;

    /**
     * getImportResolver
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
     * addToImportPool
     *
     * @param array $pool
     * @param string $string
     *
     * @return void
     */
    protected function addToImportPool(array &$pool, $string)
    {
        $this->getImportResolver()->add($string);
        $pool[] = $string;
    }
}
