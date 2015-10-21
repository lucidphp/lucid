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

    const DESC_SHORT = 234;

    const DESC_LONG = 235;

    /**
     * description
     *
     * @var array
     */
    private $description;

    /**
     * annotations
     *
     * @var array
     */
    private $annotations;

    /**
     * returnAnnotation
     *
     * @var string|null
     */
    private $returnAnnotation;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->description = [];
        $this->annotations = [];
    }

    /**
     * setDescription
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
     * hasDescription
     *
     * @return boolean
     */
    public function hasDescription()
    {
        return isset($this->description[self::DESC_SHORT]);
    }

    /**
     * hasLongDescription
     *
     * @return boolean
     */
    public function hasLongDescription()
    {
        return isset($this->description[self::DESC_LONG]);
    }

    /**
     * setLongDescription
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
     * getDescription
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description[self::DESC_SHORT];
    }

    /**
     * getLongDescription
     *
     * @return string|null
     */
    public function getLongDescription()
    {
        return $this->description[self::DESC_LONG];
    }

    /**
     * setAnnotations
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
     * addParam
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
     * addAnnotation
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
     * unshiftAnnotation
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
     * hasAnnotations
     *
     * @return boolean
     */
    public function hasAnnotations()
    {
        return !empty($this->annotations);
    }

    /**
     * setReturn
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

        return $raw ? $writer : $writer->dump();
    }

    /**
     * isEmpty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !$this->hasDescription() && !$this->hasLongDescription() &&
            empty($this->annotations) &&  null === $this->returnAnnotation;
    }

    /**
     * openBlock
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function openBlock(Writer $writer)
    {
        return $writer->writeln('/**');
    }

    /**
     * closeBlock
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function closeBlock(Writer $writer)
    {
        return $writer->writeln(' */');
    }

    /**
     * writeBlock
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function writeBlock(Writer $writer)
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

    public function copy()
    {
        $newDoc = new self;
        if ($this->hasDescription()) {
            $newDoc->setDescription($this->description[self::DESC_SHORT]);
        }
        if ($this->hasLongDescription()) {
            $newDoc->setLongDescription($this->description[self::DESC_LONG]);
        }

        foreach ($this->annotations as $annotation) {
            list ($a, $b) = array_pad((array)$annotation, 2, null);
            $newDoc->addAnnotation($a, $b);
        }

        return $newDoc;
    }

    /**
     * blockLine
     *
     * @param Writer $writer
     * @param array $lines
     *
     * @return void
     */
    protected function blockLines(Writer $writer, array $lines)
    {
        foreach ($lines as $line) {
            $writer->writeln(' * ' . $line);
        }
    }
}
