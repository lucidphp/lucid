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

use Lucid\Writer\Stringable;
use Lucid\Writer\Writer;
use Lucid\Writer\WriterInterface;
use Lucid\Writer\GeneratorInterface;

/**
 * @class DocBlock
 * @see GeneratorInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DocBlock implements GeneratorInterface
{
    use Stringable;

    /** @var int */
    const DESC_SHORT = 234;

    /** @var int */
    const DESC_LONG = 235;

    /** @var array */
    private $description = [];

    /** @var array */
    private $annotations = [];

    /** @var string|null */
    private $returnAnnotation;

    /** @var bool */
    private $inline = false;

    /** @var bool */
    private $setInline = false;

    /**
     * Sets the short description.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description[self::DESC_SHORT] = $description;
    }

    /**
     * setInline
     *
     * @param bool $inline
     *
     * @return void
     */
    public function setInline($inline)
    {
        $this->inline = (bool)$inline;
    }

    /**
     * Check if a short description is set.
     *
     * @return bool
     */
    public function hasDescription()
    {
        return isset($this->description[self::DESC_SHORT]);
    }

    /**
     * Check if the long description is set.
     *
     * @return boolean
     */
    public function hasLongDescription()
    {
        return isset($this->description[self::DESC_LONG]);
    }

    /**
     * Sets the long description.
     *
     * @param string $description
     *
     * @return void
     */
    public function setLongDescription($description)
    {
        $this->description[self::DESC_LONG] = $description;
    }

    /**
     * Gets the short description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description[self::DESC_SHORT];
    }

    /**
     * Gets the long description.
     *
     * @return string|null
     */
    public function getLongDescription()
    {
        return $this->description[self::DESC_LONG];
    }

    /**
     * Sets the annotations.
     *
     * @param array $annotations
     *
     * @return void
     */
    public function setAnnotations(array $annotations)
    {
        $this->annotations = [];

        foreach ($annotations as $annotation) {
            if (is_array($annotation)) {
                list ($name, $desc) = array_pad($annotation, 2, null);
                $this->addAnnotation($name, $desc);
            } else {
                $this->addAnnotation(null);
            }
        }
    }

    /**
     * Adds a param annotation.
     *
     * @param string $type
     * @param string $var
     * @param string $description
     *
     * @return void
     */
    public function addParam($type, $var, $description = null)
    {
        $this->annotations[] = ['param', $type  . ' $'.$var . (null ===$description ? '' : ' ' .$description)];
    }

    /**
     * Adds an annotation.
     *
     * @param string $name
     * @param string $description
     *
     * @return void
     */
    public function addAnnotation($name, $description = null)
    {
        $this->annotations[] = null === $name ? null : [$name, $description];
    }

    /**
     * Adds an annotation to the top.
     *
     * @param string $name
     * @param string $description
     *
     * @return void
     */
    public function unshiftAnnotation($name, $description = null)
    {
        $this->addAnnotation($name, $description);

        array_unshift($this->annotations, array_pop($this->annotations));
    }

    /**
     * Checks if annotations are set.
     *
     * @return bool
     */
    public function hasAnnotations()
    {
        return !empty($this->annotations);
    }

    /**
     * Sets the return annotation.
     *
     * @param string $type
     * @param string $description
     *
     * @return void
     */
    public function setReturn($type, $description = null)
    {
        $this->returnAnnotation = [$type, $description];
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = self::RV_STRING)
    {
        $writer = new Writer;

        $this->openBlock($writer);
        $this->writeBlock($writer);
        $this->closeBlock($writer);

        if ($this->setInline) {
            $ln2 = $writer->popln();
            $ln1 = $writer->popln();
            $writer->appendln($ln1.$ln2);

            $this->setInline = false;
        }

        return $raw ? $writer : $writer->dump();
    }

    /**
     * Check if the dock block is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->hasDescription() && !$this->hasLongDescription() &&
            empty($this->annotations) &&  null === $this->returnAnnotation;
    }

    /**
     * Writes the opening dockblock line.
     *
     * @param WriterInterface $writer
     *
     * @return WriterInterface
     */
    protected function openBlock(WriterInterface $writer)
    {
        return $writer->writeln('/**');
    }

    /**
     * Writes the closing dockblock line.
     *
     * @param WriterInterface $writer
     *
     * @return WriterInterface
     */
    protected function closeBlock(WriterInterface $writer)
    {
        return $writer->writeln(' */');
    }

    /**
     * Writes descriptions and annotations to the Writer instance.
     *
     * @param WriterInterface $writer
     *
     * @return void
     */
    protected function writeBlock(WriterInterface $writer)
    {
        $newline = false;
        $lnbuff = [];

        if ($this->hasDescription()) {
            $newline = true;
            foreach (explode("\n", $this->description[self::DESC_SHORT]) as $line) {
                $lnbuff[] = $line;
            }
        }

        if ($this->hasLongDescription()) {
            if ($newline) {
                $lnbuff[] = '';
            }

            $newline = true;
            foreach (explode("\n", $this->description[self::DESC_LONG]) as $line) {
                $lnbuff[] = $line;
            }
        }

        if ($this->hasAnnotations() && $newline) {
            $lnbuff[] = '';
        } else {
            $newline = $newline;
        }

        foreach ($this->annotations as $annotation) {
            if (null === $annotation) {
                $lnbuff[] = '';
                continue;
            }

            list ($name, $desc) = $annotation;

            $anno = '@'.$name . (null === $desc ? '' : ' ' .$desc);

            foreach (explode("\n", $anno) as $annot) {
                $lnbuff[] = $annot;
            }
        }

        if (null !== $this->returnAnnotation) {
            if ($newline) {
                $lnbuff[] = '';
            }

            list ($type, $desc) = $this->returnAnnotation;

            foreach (explode("\n", $anno = '@return '.$type . (null === $desc ? '' : ' ' .$desc)) as $annot) {
                $lnbuff[] = $annot;
            }
        }

        $this->blockLines($writer, $lnbuff);
    }

    /**
     * blockLine
     *
     * @param WriterInterface $writer
     * @param array $lines
     *
     * @return void
     */
    protected function blockLines(WriterInterface $writer, array $lines)
    {
        if ($this->inline && 1 === count($lines)) {
            $this->setInline = true;
            $prefix = ' ';
        } else {
            $prefix = ' * ';
        }

        foreach ($lines as $line) {
            $writer->writeln($prefix . $line);
        }
    }
}
