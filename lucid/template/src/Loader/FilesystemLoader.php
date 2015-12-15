<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Loader;

use Lucid\Template\Template;
use Lucid\Template\IdentityInterface;
use Lucid\Template\Resource\FileResource;
use Lucid\Template\Exception\LoaderException;

/**
 * @class FilesystemLoader
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class FilesystemLoader implements LoaderInterface
{
    private $paths;
    private $loaded;

    /**
     * Constructor.
     *
     * @param string|array $paths include paths to look for templates.
     */
    public function __construct($paths)
    {
        $this->paths = is_array($paths) ? $paths : explode(',', $paths);
        $this->loaded = [];
    }

    /**
     * {@inheritdoc}
     */
    public function load(IdentityInterface $template)
    {
        if (isset($this->loaded[$template->getName()])) {
            return $this->loaded[$template->getName()];
        }

        if (!$realpath = $this->findTemplateInPaths($template->getName())) {
            throw LoaderException::templateNotFound($template);
        }

        return $this->loaded[$template->getName()] = new FileResource($realpath);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(IdentityInterface $template, $now)
    {
        try {
            return filemtime($this->load($template)->getResource()) < $now;
        } catch (LoaderException $e) {
        }

        return false;
    }

    /**
     * findTemplateInPaths
     *
     * @param string $template
     *
     * @return string|boolean
     */
    private function findTemplateInPaths($template)
    {
        if (file_exists($template)) {
            return $template;
        }

        foreach ($this->paths as $path) {
            if (file_exists($realpath = $path . DIRECTORY_SEPARATOR . ltrim($template, DIRECTORY_SEPARATOR))) {
                return $realpath;
            }
        }

        return false;
    }
}
