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
use Lucid\Writer\Stringable;
use Lucid\Common\Helper\Arr;
use Lucid\Writer\GeneratorInterface;

/**
 * @class JsonGenerator
 * @package Selene\Module\Writer\Generator\File
 * @version $Id$
 */
class JsonGenerator implements GeneratorInterface
{
    use Stringable;

    /**
     * content
     *
     * @var array
     */
    private $payload;

    /**
     * Constructor.
     *
     * @param array $contents
     */
    public function __construct(array $payload = [])
    {
        $this->setContent($payload);
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
        $this->payload = [];

        array_walk($contents, function ($val, $key) {
            $this->addContent($key, $val);
        });

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
        Arr::set($this->payload, $key, $content);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $writer = (new Writer)
            ->writeln(json_encode($this->payload, JSON_PRETTY_PRINT));

        return $raw ? $writer : $writer->dump();
    }
}
