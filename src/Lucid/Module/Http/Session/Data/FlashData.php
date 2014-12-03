<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Data;

class FlashData extends NamespacedAttributes implements MessagesInterface
{
    const CURRENT_KEY  = '_current';
    const FORMER_KEY  = '_then';
    const DEFAULT_STORE_KEY  = '_flashes_';
    const DEFAULT_STORE_NAME = 'lucid_flashes';

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $key
     */
    public function __construct($name = self::DEFAULT_STORE_NAME, $key = self::DEFAULT_STORE_KEY)
    {
        $this->name = (string)$name;
        $this->key = (string)$key;
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, $default = [])
    {
        return parent::get(static::CURRENT_KEY.'.'.$type, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function flush($type, $default = [])
    {
        $result = parent::get(static::CURRENT_KEY.'.'.$type, $default);

        $this->remove($type);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function flushAll()
    {
        $messages = $this->parameters;
        $this->parameters = [];

        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function setAll(array $messages)
    {
        $this->initialize($messages);
    }
}
