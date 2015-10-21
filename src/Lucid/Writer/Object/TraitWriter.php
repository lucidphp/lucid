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
 * @class TraitWriter
 * @see AbstractWriter
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TraitWriter extends AbstractWriter
{
    use TraitAwareWriterHelper;

    /**
     * Constructor;
     *
     * @param string $name
     * @param string $namespace
     */
    public function __construct($name, $namespace = null)
    {
        parent::__construct($name, $namespace, T_TRAIT);

        $this->traits = [];
        $this->properties = [];
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
                sprintf('Trait method %s must not be instance of "InterfaceMethod".', $method->getName())
            );
        }

        parent::addMethod($method);
    }

    /**
     * hasItemsBeforeMethods
     *
     * @return boolean
     */
    protected function hasItemsBeforeMethods()
    {
        return !empty($this->traits) || !empty($this->properties);
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
    protected function prepareObjDoc(DocBlock $block)
    {
        return $block;
    }

    /**
     * {@inheritdoc}
     */
    protected function getImports()
    {
        return array_merge($this->uses, $this->traits);
    }
}
