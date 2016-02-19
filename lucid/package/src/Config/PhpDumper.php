<?php

/*
 * This File is part of the Lucid\Package\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Config;

use Lucid\Writer\Writer;
use Lucid\Writer\FormatterTrait;
use Lucid\Writer\Object\DocComment;

/**
 * @class PhpDumper
 * @see DumperInterface
 *
 * @package Lucid\Package\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpDumper implements DumperInterface
{
    use FormatterTrait;

    /** @var string */
    private $cnf;

    /**
     * Constructor.
     *
     * @param string $cnf
     */
    public function __construct($cnf = 'config')
    {
        $this->cnf = $cnf;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return $cnf.'.php';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return 'php' === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($name, array $contents = [], $format = null)
    {
        if (!$this->supports($format)) {
            return;
        }

        $comment = new DocComment(
            'This file was automatically created',
            'Created at ' . (new \DateTime())->format('Y-m-d:H:m:s') . '.',
            [],
            0
        );

        return (string)(new Writer)
            ->writeln('<?php')
            ->newline()
            ->writeln($comment)
            ->newline()
            ->writeln('$builder->addConfig(')
            ->indent()
                ->writeln("'$name'" . ', ')
                ->writeln($this->extractParams($contents))
            ->outdent()
            ->writeln(');')
            ->outdent();
    }
}
