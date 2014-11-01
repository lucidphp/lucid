<?php

/*
 * This File is part of the Lucid\Module\Template\Loader package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Loader;

use Lucid\Module\Template\TemplateInterface;
use Lucid\Module\Template\Resource\FileResource;
use Lucid\Module\Template\Exception\LoaderException;

/**
 * @class FilesystemLoader
 *
 * @package Lucid\Module\Template\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemLoader implements LoaderInterface
{
    private $paths;

    /**
     * Constructor.
     *
     * @param string|array $paths include paths to look for templates.
     */
    public function __construct($paths)
    {
        $this->paths = is_array($paths) ? $paths : explode(',', $paths);
    }

    /**
     * {@inheritdoc}
     */
    public function load(TemplateInterface $template)
    {
        if (!$realpath = $this->findTemplateInPaths($template->getPath())) {
            throw LoaderException::templateNotFound($template);
        }

        return new FileResource($realpath);
    }

    /**
     * findTemplateInPaths
     *
     * @param string $template
     *
     * @return string|boolean
     */
    protected function findTemplateInPaths($template)
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
