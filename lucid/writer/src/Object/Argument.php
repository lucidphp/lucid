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
 * @class Argument
 * @see GeneratorInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Argument implements GeneratorInterface
{
    use Stringable;

    /**
     * name
     *
     * @var string
     */
    private $name;

    /**
     * type
     *
     * @var string
     */
    private $type;

    /**
     * default
     *
     * @var string
     */
    private $default;

    /**
     * isReference
     *
     * @var mixed
     */
    private $isReference;

    /**
     * primitives
     *
     * @var array
     */
    private static $primitives = [
        'void',
        'bool',
        'boolean',
        'int',
        'integer',
        'float',
        'double',
        'string',
        'object',
        'mixed',
        'null'
    ];

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $default
     */
    public function __construct($name, $type = null, $default = null, $isRef = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->default = $default;
        $this->isReference = $isRef;
    }

    /**
     * Get the argument name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value type.
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the argument type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set or unset the default value
     *
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * isReference
     *
     * @param mixed $ref
     *
     * @return void
     */
    public function isReference($ref)
    {
        $this->isReference = (bool)$ref;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $writer = new Writer;
        $prefix = $this->isReference ? '&' : '';

        $type = null === $this->type || in_array($this->type, static::$primitives) ? '' : $this->type . ' ';

        if (null !== $this->default) {
            $line = sprintf('%s%s$%s = %s', $type, $prefix, $this->name, $this->default);
        } else {
            $line = sprintf('%s%s$%s', $type, $prefix, $this->name);
        }

        $writer->writeln($line);

        return $raw ? $writer : $writer->dump();
    }
}
