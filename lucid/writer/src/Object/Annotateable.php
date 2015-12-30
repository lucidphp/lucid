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
 * @class Annotateable
 * @see MemberInterface
 * @abstract
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class Annotateable implements MemberInterface
{
    /** @var DocBlock */
    private $docBlock;

    /** @var bool */
    private $inline;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->docBlock = new DocBlock;
    }

    /**
     * Set the doc block comment
     *
     * @param mixed $comment
     *
     * @return void
     */
    public function addAnnotation($name, $desc = null)
    {
        $this->docBlock->addAnnotation($name, $desc);
    }

    /**
     * addParam
     *
     * @param string $type
     * @param string $var
     * @param string $desc
     *
     * @return void
     */
    public function addParam($type, $var, $desc = null)
    {
        $this->docBlock->addParam($type, $var, $desc);
    }

    /**
     * Set the doc block comment
     *
     * @param mixed $comment
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->docBlock->setDescription($description);
    }

    /**
     * setLongDescription
     *
     * @return void
     */
    public function setLongDescription($description)
    {
        $this->docBlock->setLongDescription($description);
    }

    /**
     * getDocBlock
     *
     * @return Writer|string
     */
    final protected function getDoc(Writer $writer, $asString = false)
    {
        $this->prepareAnnotations($block = clone $this->docBlock);

        $writer->writeln($block);

        return $asString ? $writer->dump() : $writer;
    }

    /**
     * prepareAnnotations
     *
     * @return array
     */
    abstract protected function prepareAnnotations(DocBlock $block);
}
