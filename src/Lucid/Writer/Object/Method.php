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

    /**
     * visibility
     *
     * @var string
     */
    private $visibility;

    /**
     * type
     *
     * @var string
     */
    private $type;

    /**
     * prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * name
     *
     * @var string
     */
    private $name;

    /**
     * body
     *
     * @var mixed
     */
    private $body;

    /**
     * abstract
     *
     * @var boolean
     */
    private $abstract;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visibility
     * @param string $type
     * @param boolean $static
     */
    public function __construct($name, $visibility = self::IS_PUBLIC, $type = self::T_VOID, $static = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->visibility = $visibility;
        $this->arguments = [];
        $this->setStatic($static);
        $this->abstract = false;

        parent::__construct();
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set this method stattic.
     *
     * @param boolean $static
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
     * @param boolean $abstract
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

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
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

        $this->getDoc($writer, false)
            ->writeln(
                sprintf(
                    '%s%s%s function %s(%s)',
                    $abs,
                    $this->visibility,
                    $this->prefix,
                    $this->name,
                    $this->getArguments()
                )
            );
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
