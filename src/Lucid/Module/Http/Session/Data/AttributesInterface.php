<?php

/*
 * This File is part of the Lucid\Module\Http\Session\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Data;

use Lucid\Module\Http\ParameterMutableInterface;

/**
 * @class AttributesInterface
 *
 * @package Lucid\Module\Http\Session\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface AttributesInterface extends ParameterMutableInterface
{
    /**
     * clear
     *
     * @return void
     */
    public function clear();

    /**
     * initialize
     *
     * @param array $data
     *
     * @return void
     */
    public function initialize(array &$data);

    /**
     * replace
     *
     * @param array $data
     *
     * @return void
     */
    public function replace(array $data);

    /**
     * getKey
     *
     * @return void
     */
    public function getKey();

    /**
     * getName
     *
     * @return void
     */
    public function getName();
}
