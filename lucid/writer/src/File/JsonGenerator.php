<?php

/*
 * This File is part of the Selene\Module\Writer\Generator\File package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\File;

use Lucid\Writer\Writer;
use Lucid\Writer\GeneratorInterface;
use Lucid\Common\Helper\Arr;

/**
 * @class JsonGenerator
 * @package Selene\Module\Writer\Generator\File
 * @version $Id$
 */
class JsonGenerator implements GeneratorInterface
{
    /**
     * content
     *
     * @var array
     */
    protected $content;

    /**
     * Constructor.
     *
     * @param array $contents
     */
    public function __construct(array $contents = [])
    {
        $this->setContent($contents);
    }

    /**
     * setContent
     *
     * @param array $contents
     *
     * @return JsonGenerator
     */
    public function setContent(array $contents)
    {
        $this->content = [];
        $this->doSetContents($contents);

        return $this;
    }

    /**
     * addContent
     *
     * @param string $key
     * @param mixed $content
     *
     * @return JsonGenerator
     */
    public function addContent($key, $content)
    {
        Arr::set($this->content, $key, $content);

        return $this;
    }

    /**
     * generate
     *
     * @param mixed $raw
     *
     * @return string|Writer
     */
    public function generate($raw = false)
    {
        $writer = new Writer;

        $writer
            ->writeln(json_encode($this->getContent(), JSON_PRETTY_PRINT));

        return $raw ? $writer : $writer->dump();
    }

    /**
     * getContent
     *
     * @return array
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * doSetContents
     *
     * @param array $contents
     *
     * @return void
     */
    protected function doSetContents(array $contents)
    {
        foreach ($contents as $key => $content) {
            $this->addContent($key, $content);
        }
    }
}
