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

use Psr\Log\LoggerInterface;
use Lucid\Template\IdentityInterface;

/**
 * @class LoggerAwareLoader
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class LoggerAwareLoader implements LoaderInterface
{
    /**
     * logger
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * loader
     *
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param LoggerInterface $logger
     */
    public function __construct(LoaderInterface $loader, LoggerInterface $logger)
    {
        $this->loader = $loader;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function load(IdentityInterface $template)
    {
        if (!$res = $this->loader->load($template)) {

            $this->logger->error('Error loading template "%s".', [(string)$template]);
            return;
        }

        $this->logger->info('Loaded template "%s".', [(string)$template]);

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(IdentityInterface $template, $now)
    {
        return $this->loader->isValid($template, $now);
    }
}
