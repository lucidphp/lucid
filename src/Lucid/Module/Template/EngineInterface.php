<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

/**
 * @interface EngineInterface
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface EngineInterface
{
    /**
     * Renders a Template with given parameters.
     *
     * @param mixed $template
     * @param array $parameters
     *
     * @return string the rendererd template.
     */
    public function render($template, array $parameters = []);

    /**
     * Get the supported template type as string
     *
     * @return string
     */
    public function getType();

    /**
     * supports
     *
     * @param string $type
     *
     * @return boolean
     */
    public function supports($type);
}
