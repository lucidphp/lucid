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

/**
 * @class Method
 * @see MethodInterface
 * @see Annotateable
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Method extends Annotateable implements MethodInterface
{
    use Stringable;

    /** @var string */
    private $visibility;

    /** @var string */
    private $type;

    /** @var string */
    private $prefix;

    /** @var string */
    private $name;

    /** @var string */
    private $body;

    /** @var bool */
    private $abstract;

    /** @var bool */
    private $php7 = false;

    /** @var array */
    private static $magick = [
        '__clone',    '__call',   '__callstatic', '__construct',
        '__destruct', '__invoke', '__tostring',   '__get',
        '__set',      '__sleep',  '__wakeup',     '__debuginfo',
        '__unset',    '__isset',  '__set_state',
    ];

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visibility
     * @param string $type
     * @param bool $static
     */
    public function __construct($name, $visibility = self::IS_PUBLIC, $type = self::T_VOID, $static = false)
    {
        $this->name       = $name;
        $this->type       = $type;
        $this->visibility = $visibility;
        $this->arguments  = [];
        $this->abstract   = false;
        $this->setStatic($static);

        parent::__construct();
    }

    /**
     * Get tht method name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if this method is magick.
     *
     * @return bool
     */
    public function isMagick()
    {
        return in_array(strtolower($this->name), self::$magick);
    }

    /**
     * Check if this method is a Constructor/Destructor.
     *
     * @return bool
     */
    public function isConstructor()
    {
        return in_array(strtolower($this->name), ['__constrct', '__destruct']);
    }

    /**
     * Set this method stattic.
     *
     * @param bool $static
     *
     * @return void
     */
    public function setStatic($static)
    {
        $this->prefix = (bool)$static ? ' static' : '';
    }

    /**
     * Set this method abstract
     *
     * @param bool $abstract
     *
     * @return void
     */
    public function setAbstract($abstract)
    {
        $this->abstract = (bool)$abstract;
    }

    /**
     * Sets the method return type.
     *
     * @param string $type
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Set the method arguments
     *
     * @param array $arguments an array of Argument instances.
     *
     * @return void
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = [];

        array_map([$this, 'addArgument'], $arguments);
    }

    /**
     * Add an argument
     *
     * @param Argument $argument
     *
     * @return void
     */
    public function addArgument(Argument $argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * Set the method body.
     *
     * @param string|Writer $body
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $writer = new Writer(4, true);
        $writer->setOutputIndentation(1);

        $abstract = $this->abstract;

        $this->getMethodDeclaration($writer, $abstract);

        if (!$abstract) {
            $writer
                ->writeln('{')
                ->indent()
                ->writeln($this->getBody())
                ->outdent()
                ->writeln('}');
        } else {
            $writer
                ->appendln(';');
        }

        return $raw ? $writer : $writer->dump();
    }

    /**
     * getBody
     *
     * @return string
     */
    protected function getBody()
    {
        if (null === $this->body) {
            return;
        }

        if ($this->body instanceof Writer) {
            $body = $this->body;
        } else {
            $body = new Writer;
            $body->writeln($this->body);
        }

        $body->setOutputIndentation(0);

        return $body->dump();
    }

    /**
     * getMethodDeclaration
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function getMethodDeclaration(Writer $writer, $abstract)
    {
        $abs = $abstract ? 'abstract ' : '';

        $str = '%s%s%s function %s(%s)';

        if ($this->isPhp7Strict()) {
            $str .= sprintf(' : %s', $this->type);
        }

        $this->getDoc($writer, false)
            ->writeln(
                sprintf(
                    $str,
                    $abs,
                    $this->visibility,
                    $this->prefix,
                    $this->name,
                    $this->getArguments()
                )
            );
    }

    protected function isPhp7Strict()
    {
        return false;
    }

    /**
     * getArguments
     *
     * @return string
     */
    protected function getArguments()
    {
        if (empty($this->arguments)) {
            return '';
        }

        $args = [];

        foreach ($this->arguments as $argument) {
            $args[] = $argument->generate();
        }

        return implode(', ', $args);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareAnnotations(DocBlock $block)
    {
        if (!$block->hasDescription()) {
            $block->setDescription($this->name);
        }

        foreach ($this->arguments as $argument) {
            $block->addParam($argument->getType(), $argument->getName());
        }

        if ('__construct' !== $this->name) {
            $block->setReturn($this->type);
        }
    }
}
