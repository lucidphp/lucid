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

use InvalidArgumentException;
use Lucid\Writer\WriterInterface;

/**
 * @class ClassWriter
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ClassWriter extends InterfaceWriter
{
    use TraitAwareWriterHelper;

    /** @var bool */
    private $abstract;

    /** @var array */
    private $interfaces;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $namespace
     * @param string $parent
     */
    public function __construct($name, $namespace = null, $parent = null)
    {
        $this->traits     = [];
        $this->interfaces = [];
        $this->properties = [];
        $this->abstract   = false;

        parent::__construct($name, $namespace, $parent);
    }

    /**
     * Marks the class abstract.
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
     * Adds an interface to the class definition.
     *
     * @param string $interface
     *
     * @return void
     */
    public function addInterface($interface)
    {
        $this->addToImportPool($this->interfaces, $interface);
    }

    /**
     * {@inheritdoc}
     */
    public function addMethod(MethodInterface $method)
    {
        if ($method instanceof InterfaceMethod) {
            throw new InvalidArgumentException(
                sprintf('Class method %s must not be instance of "InterfaceMethod".', $method->getName())
            );
        }

        AbstractWriter::addMethod($method);
    }

    /**
     * {@inheritdoc}
     */
    protected function hasItemsBeforeMethods()
    {
        return parent::hasItemsBeforeMethods() || !empty($this->traits) || !empty($this->properties);
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectDeclarationExtension()
    {
        $dcl = parent::getObjectDeclarationExtension();

        if (empty($this->interfaces)) {
            return $dcl;
        }

        $aliases = [];

        foreach ($this->interfaces as $interface) {
            $aliases[] = $this->getImportResolver()->getAlias($interface);
        }

        return $dcl . ' implements ' . implode(', ', $aliases);
    }

    /**
     * {@inheritdoc}
     */
    protected function writeObjectBody(WriterInterface $writer)
    {
        $this->writeTraits($writer, $resolver = $this->getImportResolver());
        $this->writeProperties($writer, $resolver);

        return parent::writeObjectBody($writer);
    }

    /**
     * {@inheritdoc}
     */
    protected function getImports()
    {
        return array_merge($this->uses, $this->interfaces, $this->traits, $this->interfaces);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTypeConstant()
    {
        return T_CLASS;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTypePrefix()
    {
        return $this->abstract ? 'abstract ' : null;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareObjDoc(DocBlock $doc)
    {
        $resolver = $this->getImportResolver();

        if (!empty($this->interfaces)) {
            foreach (array_reverse($this->interfaces) as $interface) {
                $name = $resolver->hasAlias($interface) ? $resolver->getAlias($interface) : $interface;
                $doc->unshiftAnnotation('see', self::trimNs($name));
            }
        }

        if ($this->parent) {
            $name = $resolver->hasAlias($this->getParent()) ?
                $resolver->getAlias($this->getParent()) :
                $this->getParent();
            $doc->unshiftAnnotation('see', self::trimNs($name));
        }

        $doc->unshiftAnnotation($this->getType(), $this->getName());
    }
}
