<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Listener;

use Lucid\Template\Data\TemplateDataInterface;

/**
 * @interface ListenerInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ListenerInterface
{
    /**
     * onRender
     *
     * @param View $view
     *
     * @return void
     */
    public function onRender(TemplateDataInterface $data);
}
