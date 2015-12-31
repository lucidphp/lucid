<?php

/*
 * This File is part of the Lucid\WriterInterface package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

use Lucid\Writer\WriterInterface;

/**
 * @trait TraitHelper
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait TraitAwareWriterHelper
{
    use ImportHelper;

    /**
     * traits
     *
     * @var array
     */
    protected $traits;

    /**
     * properties
     *
     * @var array
     */
    protected $properties;

    /**
     * replacements
     *
     * @var array
     */
    protected $replacements;

    /**
     * setProperties
     *
     * @param array $props
     *
     * @return void
     */
    public function setProperties(array $props)
    {
        foreach ($props as $prop) {
            $this->addProperty($prop);
        }
    }

    /**
     * addProperty
     *
     * @param Property $prop
     *
     * @return void
     */
    public function addProperty(Property $prop)
    {
        $this->properties[] = $prop;
    }

    /**
     * addTrait
     *
     * @param mixed $trait
     *
     * @return void
     */
    public function addTrait($trait)
    {
        $this->addToImportPool($this->traits, $trait);
    }

    /**
     * useTraitMethodAs
     *
     * @param string $trait
     * @param string $method
     * @param string $replacement
     *
     * @return void
     */
    public function useTraitMethodAs($trait, $method, $replacement, $visibility = MemberInterface::IS_PUBLIC)
    {
        $this->replacements['trait_use_as'][] = [$trait, $method, $replacement, $visibility];
    }

    /**
     * replaceTraitConflict
     *
     * @param string $trait
     * @param string $conflict
     * @param string $method
     *
     * @return void
     */
    public function replaceTraitConflict($trait, $conflict, $method)
    {
        $this->replacements['trait_conflict'][] = [$trait, $conflict, $method];
    }

    /**
     * writeProperties
     *
     * @param WriterInterface $writer
     * @param ImportResolver $resolver
     *
     * @return void
     */
    protected function writeProperties(WriterInterface $writer, ImportResolver $resolver)
    {
        if (empty($this->properties)) {
            return;
        }

        if (!empty($this->traits)) {
            $writer->newline();
        }

        foreach ((array)$this->properties as $prop) {
            $writer->writeln($prop->generate())->newline();
        }

        if (empty($this->methods)) {
            $writer->popln();
        }
    }

    /**
     * writeTraits
     *
     * @param WriterInterface $writer
     * @param ImportResolver $resolver
     * @param mixed $newLine
     *
     * @return void
     */
    protected function writeTraits(WriterInterface $writer, ImportResolver $resolver, $newLine = false)
    {
        if (empty($this->traits)) {
            return;
        }

        $traits = $this->traits;

        $writer
            ->indent()
            ->writeln('use ' . $resolver->getAlias(array_shift($traits)))
            ->indent();

        foreach ($traits as $trait) {
            $writer
                ->appendln(',')
                ->writeln($resolver->getAlias($trait));
        }

        $writer->outdent();
        $this->completeTraitList($writer, $resolver);
        $writer->outdent();
    }

    /**
     * completeTraitList
     *
     * @param WriterInterface $writer
     * @param ImportResolver $resolver
     *
     * @return void
     */
    protected function completeTraitList(WriterInterface $writer, ImportResolver $resolver)
    {
        $useRpl = [];
        $cflRpl = [];

        $replUse = isset($this->replacements['trait_use_as']);
        $replCfl = isset($this->replacements['trait_conflict']);

        foreach ($this->traits as $trait) {

            if ($replUse) {
                $useRpl = array_merge(
                    $useRpl,
                    array_filter($this->replacements['trait_use_as'], function ($def) use ($trait) {
                        return AbstractWriter::trimNs($trait) === AbstractWriter::trimNs($def[0]);
                    })
                );
            }

            if ($replCfl) {
                $cflRpl = array_merge(
                    $cflRpl,
                    array_filter($this->replacements['trait_conflict'], function ($def) use ($trait, $resolver) {
                        // conflicting trait exists:
                        $cflExists = false;

                        foreach ($this->traits as $strait) {
                            if ($clfExists = (AbstractWriter::trimNs($strait) === AbstractWriter::trimNs($def[1]))) {
                                break;
                            }
                        }

                        return $clfExists && (AbstractWriter::trimNs($trait) === AbstractWriter::trimNs($def[0]));
                    })
                );

            }
        }

        if (empty($useRpl) && empty($cflRpl)) {
            $writer->appendln(';');

            return;
        }

        $writer
            ->appendln(' {')
            ->indent();


        foreach ($useRpl as $urpl) {
            list ($trait, $method, $replacement, $visibility) = $urpl;

            $this->writeUseReplacement($writer, $resolver->getAlias($trait), $method, $replacement, $visibility);
        }

        foreach ($cflRpl as $crpl) {
            list ($trait, $conflict, $method) = $crpl;

            $this->writeConflictReplacement(
                $writer,
                $resolver->getAlias($trait),
                $method,
                $resolver->getAlias($conflict)
            );
        }

        $writer
            ->outdent()
            ->writeln('}');
    }

    /**
     * writeUseReplacement
     *
     * @param WriterInterface $writer
     * @param string $alias
     * @param string $method
     * @param string $replacement
     * @param string $visibility
     *
     * @return void
     */
    protected function writeUseReplacement(WriterInterface $writer, $alias, $method, $replacement, $visibility = null)
    {
        $visibility = $visibility ? sprintf('%s ', $visibility) : '';

        $writer->writeln(sprintf('%s::%s as %s%s;', $alias, $method, $visibility, $replacement));
    }

    /**
     * writeUseReplacement
     *
     * @param WriterInterface $writer
     * @param string $alias
     * @param string $method
     * @param string $replacement
     * @param string $visibility
     *
     * @return void
     */
    protected function writeConflictReplacement(WriterInterface $writer, $alias, $method, $replacement)
    {
        $writer->writeln(sprintf('%s::%s insteadof %s;', $alias, $method, $replacement));
    }
}
