<?php

/*
 * This File is part of the Lucid\Module\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Request;

use Lucid\Module\Http\ParameterInterface;

/**
 * @interface ParametersServerInterface
 *
 * @package Lucid\Module\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface HeaderAwareParameterInterface extends ParameterInterface
{
    public function getHeaders();
}
