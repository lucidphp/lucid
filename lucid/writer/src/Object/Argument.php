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

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $default;

    /** @var bool */
    private $isReference;

    /** @var bool */
    private $isVariadic;

    /** @var array */
    private static $primitives = [
        'bool',   'boolean', 'int',    'integer', 'float',
        'double', 'string',  'object', 'mixed',
    ];

    /** @var array */
    private static $silent = ['null', 'void', 'variadic'];

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $default
     * @param bool $isRef
     * @param bool $isVariadic
     */
    public function __construct($name, $type = null, $default = null, $isRef = false, $isVariadic = false)
    {
        $this->name        = $name;
        $this->type        = $type;
        $this->default     = $default;

        $this->isReference($isRef);
        $this->isVariadic($isVariadic);
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
     * Sets or unsets the default value.
     *
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Sets the `isReference` property to true or false.
     *
     * @param bool $ref
     *
     * @return void
     */
    public function isReference($ref)
    {
        $this->isReference = (bool)$ref;
    }

    /**
     * Sets the `isVariadic` property to true or false.
     *
     * @param bool $variadic
     *
     * @return void
     */
    public function isVariadic($variadic)
    {
        $this->isVariadic = (bool)$variadic;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $writer = new Writer;

        $prefix = $this->isReference ? '&' : '';
        $prefix .= $this->isVariadic ? '...' : '';

        $type = null === $this->type || in_array($this->type, array_merge(self::$silent, self::$primitives)) ?
            '' :
            $this->type.' ';

        if (null !== $this->default) {
            $line = sprintf('%s%s$%s = %s', $type, $prefix, $this->name, $this->default);
        } else {
            $line = sprintf('%s%s$%s', $type, $prefix, $this->name);
        }

        $writer->writeln($line);

        return $raw ? $writer : $writer->dump();
    }
}
