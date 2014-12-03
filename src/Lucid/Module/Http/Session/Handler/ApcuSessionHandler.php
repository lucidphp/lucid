<?php

/*
 * This File is part of the Lucid\Module\Http\Session\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Handler;

/**
 * @class ApcSessionHandler
 *
 * @package Lucid\Module\Http\Session\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcuSessionHandler extends AbstractSessionHandler
{
    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if ($res = apcu_fetch($this->getPrefixed($sessionId))) {
            return $res;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        apcu_store($this->getPrefixed($sessionId), $data, $this->getTtl());
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return apcu_delete($this->getPrefixed($sessionId));
    }
}
