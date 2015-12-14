<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Client;

use RuntimeException;

/**
 * @class Apcu
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Apcu extends AbstractClient
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('apcu')) {
            throw new RuntimeException('APCu extension not loaded.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return apcu_exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        $res = apcu_fetch($key, $success);

        return $success ? $res : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        return apcu_store($key, $data, $expires);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return apcu_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return apcu_clear_cache('user');
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        $val = apcu_inc($key, $value, $success);

        return $success ? (int)$val : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        $val = apcu_dec($key, $value, $success);

        return $success ? (int)$val : false;
    }
}
