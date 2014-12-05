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

use Psr\Log\LoggerInterface;

/**
 * @class LoggerAwareLoader
 *
 * @package Lucid\Module\Template\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LoggerAwareLoader implements LoaderInterace
{
    protected $logger;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param LoggerInterface $logger
     */
    public function __construct(LoaderInterface $loader, LoggerInterface $logger = null)
    {
        $this->loader = $loader;
        $this->logger = $logger;
    }

    /**
     * setLogger
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function load(IdentityInterface $template)
    {
        if ($res = $this->loader->load($template)) {

        }
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(IdentityInterface $template, $now)
    {
        return $this->loader->isValid($template, $now);
    }
}
