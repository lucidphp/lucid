<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template;

/**
 * @interface ViewManagerInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ViewManagerInterface extends RenderInterface, DisplayInterface
{
    /**
     * supports
     *
     * @param mixed $template
     *
     * @return boolean
     */
    public function supports($template);

    /**
     * getEngineForTemplate
     *
     * @param mixed $template
     *
     * @return EngineInteface|null
     */
    public function getEngineForTemplate($template);

    /**
     * notifyListeners
     *
     * @param string $name
     *
     * @return void
     */
    public function notifyListeners($name);

    /**
     * Flushes the data added by a listener.
     *
     * @param string $name
     *
     * @return \Lucid\Template\Data\TemplateDataInterface
     */
    public function flushData($name);
}
