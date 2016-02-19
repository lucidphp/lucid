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

use Lucid\Xml\Writer;
use Lucid\Xml\Inflector\SimpleInflector;

/**
 * @class XmlDumper
 * @see ConfigDumperInterface
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class XmlDumper implements ConfigDumperInterface
{
    /** @var Writer */
    private $writer;

    /** @var string */
    private $cnf;

    /**
     * Constructor.
     *
     * @param Writer $writer
     * @param string $cnf
     */
    public function __construct(Writer $writer = null, $cnf = 'config')
    {
        $this->cnf = $cnf;
        $this->writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return $this->cnf.'.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return 'xml' === strtolower($format);
    }

    /**
     * {@inheritdoc}
     */
    public function dump($name, array $contents = [], $format = null)
    {
        if (!$this->supports($format)) {
            return;
        }

        $dom = $this->getXmlWriter()->writeToDom($contents, 'config');
        $dom->firstChild->setAttribute('package', $name);


        return $dom->saveXML(null, empty($contents) ? LIBXML_NOEMPTYTAG : null);
    }

    /**
     * Sets the xml writer.
     *
     * @param Writer $writer
     *
     * @return mixed
     */
    public function setWriter(Writer $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Returns an instance of `Ludic\Xml\Writer`.
     *
     * @return Writer
     */
    protected function getWriter()
    {
        if (null === $this->writer) {
            $this->writer = new Writer;
            $this->writer->setInflector(new SimpleInflector);
        }

        return $this->writer;
    }
}
