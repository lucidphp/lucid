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
class Constant extends Annotateable implements GeneratorInterface
{
    use Stringable;

    /** @var string */
    private $name;

    /** @var string */
    private $value;

    /** @var string */
    private $type;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value, $type = 'string')
    {
        $this->name  = $name;
        $this->value = $value;
        $this->type  = $type;

        parent::__construct();
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
        $this->getDoc($writer);
        $writer->setOutputIndentation(1);
        $writer->writeln(sprintf('const %s = %s;', strtoupper($this->name), $this->getValue()));

        return $raw ? $writer : $writer->dump();
    }

    protected function prepareAnnotations(DocBlock $block)
    {
        if (!$block->hasDescription()) {
            $block->setInline(true);
        }

        if ($block->hasAnnotations()) {
            $block->unshiftAnnotation(null);
        }

        $block->unshiftAnnotation('var', $this->type);
    }

    private function getValue()
    {
        if ('string' === $this->type) {
            return sprintf('\'%s\'', trim($this->value, '\'\"'));
        }

        return $this->value;
    }
}
