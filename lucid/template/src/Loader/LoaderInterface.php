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

use Lucid\Template\IdentityInterface;

/**
 * @interface LoaderInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface LoaderInterface
{
    /**
     * Load a template.
     *
     * @param IdentityInterface $template
     *
     * @return string
     */
    public function load(IdentityInterface $template);

    /**
     * isValid
     *
     * @param IdentityInterface $template
     *
     * @return void
     */
    public function isValid(IdentityInterface $template, $now);
}
