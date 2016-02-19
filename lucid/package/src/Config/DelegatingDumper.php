<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Config;

/**
 * @class DelegatingConfigDumper
 * @see ConfigDumperInterface
 * @see DelegateAbleDumperInterface
 *
 * @package Lucid\Package\Dumper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DelegatingDumper implements DumperInterface, DelegateableDumperInterface
{
    /** @var array */
    private $dumpers;

    /** @var array */
    private $current;

    /**
     * Constructor
     *
     * @param array $dumpers
     */
    public function __construct(array $dumpers = [])
    {
        $this->current = new \SplObjectStorage;
        $this->setDumpers($dumpers);
    }

    /**
     * {@inheritdoc}
     */
    public function getDumper($format)
    {
        if ($this->current && $this->current->supports($format)) {
            return $this->current;
        }

        foreach ($this->dumpers as $dumper) {
            if ($dumper->supports($format)) {
                $this->current = $dumper;

                return $this->current;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        if (null === $this->getDumper($format)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($name, array $contents = [], $format = null)
    {
        if ($dumper = $this->getDumper($format)) {
            return $dumper->dump($name, $contents, $format);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        if ($dumper === $this->current) {
            return $dumper->getFilename();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDumpers(array $dumpers)
    {
        $this->dumpers = [];

        foreach ($dumpers as $dumper) {
            $this->addDumper($dumper);
        }
    }

    public function addDumper(ConfigDumperInterface $dumper)
    {
        $this->dumpers[] = $dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function dumper(ConfigDumperInterface $dumper)
    {
        $this->dumpers[] = $dumper;
    }
}
