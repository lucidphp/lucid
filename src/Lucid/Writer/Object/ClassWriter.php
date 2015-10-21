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

/**
 * @class ClassWriter
 * @see InterfaceWriter
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ClassWriter extends InterfaceWriter
{
    use TraitAwareWriterHelper;

    private $abstract;
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
        //AbstractWriter::__construct($name, $namespace, T_CLASS);

        //$this->constants = [];

        $this->traits = [];
        $this->interfaces = [];
        $this->properties = [];
        $this->abstract = false;

        parent::__construct($name, $namespace, $parent);
    }

    public function setAbstract($abstract)
    {
        $this->abstract = (bool)$abstract;
    }

    protected function getTypeConstant()
    {
        return T_CLASS;
    }

    /**
     * addInterface
     *
     * @param mixed $interface
     *
     * @return void
     */
    public function addInterface($interface)
    {
        $this->addToImportPool($this->interfaces, $interface);
    }

    /**
     * addMethod
     *
     * @param MethodInterface $method
     *
     * @return void
     */
    public function addMethod(MethodInterface $method)
    {
        if ($method instanceof InterfaceMethod) {
            throw new \InvalidArgumentException(
                sprintf('Class method %s must not be instance of "InterfaceMethod".', $method->getName())
            );
        }

        AbstractWriter::addMethod($method);
    }

    /**
     * hasItemsBeforeMethods
     *
     * @return boolean
     */
    protected function hasItemsBeforeMethods()
    {
        return parent::hasItemsBeforeMethods() || !empty($this->traits) || !empty($this->properties);
    }

    /**
     * getObjectDeclarationExtension
     *
     * @return null|string
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
    protected function writeObjectBody(Writer $writer)
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
