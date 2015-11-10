<?php

/*
 * This File is part of the Lucid\Http\Session\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Handler;

/**
 * @class ApcSessionHandler
 *
 * @package Lucid\Http\Session\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcSessionHandler extends AbstractSessionHandler
{
    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if ($res = apc_fetch($this->getPrefixed($sessionId))) {
            return $res;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        apc_store($this->getPrefixed($sessionId), $data, $this->getTtl());
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return apc_delete($this->getPrefixed($sessionId));
    }
}
