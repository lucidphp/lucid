<?php

/*
 * This File is part of the Lucid\Template\Traits package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Traits;

use Lucid\Template\ViewManagerInterface;

/**
 * @trait ViewAwareTrait
 *
 * @package Lucid\Template\Traits
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait ViewAwareTrait
{
    private $viewManager;

    /**
     * setView
     *
     * @param ViewManagerInterface $view
     *
     * @return void
     */
    public function setManager(ViewManagerInterface $view)
    {
        $this->viewManager = $view;
    }

    /**
     * setView
     *
     * @param ViewManagerInterface $view
     *
     * @return void
     */
    public function getManager()
    {
        return $this->viewManager;
    }
}
