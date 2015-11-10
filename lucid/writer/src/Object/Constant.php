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

use Lucid\Writer\Writer;
use Lucid\Writer\Stringable;
use Lucid\Writer\GeneratorInterface;

/**
 * @class Constant
 * @see GeneratorInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Constant implements GeneratorInterface
{
    use Stringable;

    /**
     * name
     *
     * @var string
     */
    private $name;

    /**
     * value
     *
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * generate
     *
     * @param boolean $raw
     *
     * @return void
     */
    public function generate($raw = self::RV_STRING)
    {
        $writer = new Writer;
        $writer->setOutputIndentation(1);
        $writer->writeln(sprintf('const %s = %s;', strtoupper($this->name), $this->value));

        return $raw ? $writer : $writer->dump();
    }
}
