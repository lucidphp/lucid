<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session;

use SessionHandlerInterface;
use Lucid\Http\Request;
use Lucid\Http\Response;
use Lucid\Http\Cookie\Cookie;
use Lucid\Http\Session\Storage\SessionStorageInterface;

/**
 * @class SessionManager
 * @see SessionManagerInterface
 *
 * @package Lucid\Http\Session
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SessionManager implements SessionManagerInterface
{
    /**
     * store
     *
     * @var SessionStorageInterface
     */
    private $sessions;

    /**
     * Constructor.
     *
     * @param SessionHandlerInterface $handler
     * @param CookieManager $cookies
     * @param array $config
     */
    public function __construct(SessionHandlerInterface $handler, array $config = [])
    {
        $this->sessions = [];
        $this->config   = $this->mergeDefaults($config);

        $this->prepareStoreFactory($handler);
    }

    /**
     * {@inheritdoc}
     */
    public function createSession($id = null)
    {
        return $this->newSession($this->newStorage($this->getConfig('name'), $id));
    }

    /**
     * {@inheritdoc}
     */
    public function startSession(SessionInterface $session)
    {
        $return = $session->start();

        if (!$this->hasSession($session)) {
            $this->sessions[$session->getId()] = &$session;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function stopSession(SessionInterface $session)
    {
        if (!$this->hasSession($session)) {
            throw new \InvalidArgumentException('Cannot stop a session that wasn\'t started from this manager.');
        }

        return $session->save();
    }

    /**
     * {@inheritdoc}
     */
    public function hasSession(SessionInterface $session)
    {
        return null !== ($id = $session->getId()) && $this->has($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionKey()
    {
        return $this->getConfig('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionCookieKey()
    {
        $config = $this->getConfig('cookie');

        return $config['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function createCookie(SessionInterface $session)
    {
        $config = $this->getConfig('cookie');

        return new Cookie(
            $config['name'],
            $session->getId(),
            $this->getSessionLifeTime(),
            $config['path'],
            $config['domain'],
            $config['secure'],
            $config['http_only']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return isset($this->sessions[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->has($id) ? $this->sessions[$id] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isPersistent()
    {
        return (bool)$this->getConfig('persistent');
    }

    /**
     * getSessionLifeTime
     *
     * @param SessionInterface $session
     *
     * @return int
     */
    protected function getSessionLifeTime()
    {
        if (false !== $this->getConfig('force_expiry')) {
            return 0;
        }

        $time = ($seconds = (int)$this->getConfig('lifetime') * 60) + time();

        return $seconds;
    }

    /**
     * prepareStoreFactory
     *
     * @param SessionHandlerInterface $handler
     * @param string $name
     *
     * @return void
     */
    protected function prepareStoreFactory(SessionHandlerInterface $handler)
    {
        $this->storeFactory = function ($name = null, $id = null) use (&$handler) {
            return new Storage($handler, null, $name, $id);
        };
    }

    /**
     * newStorage
     *
     * @return StorageInterface
     */
    protected function newStorage($name = null, $id = null)
    {
        return call_user_func($this->storeFactory, $name, $id);
    }

    /**
     * createSession
     *
     * @param mixed $name
     * @param mixed $id
     *
     * @return void
     */
    protected function newSession(StorageInterface $storage)
    {
        return new Session($storage);
    }

    /**
     * getConfig
     *
     * @param mixed $name
     * @param mixed $default
     *
     * @return void
     */
    protected function getConfig($name, $default = null)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : $default;
    }

    /**
     * mergeDefaults
     *
     * @param array $config
     *
     * @return void
     */
    protected function mergeDefaults(array $config = [])
    {
        return array_merge([
            'lifetime' => 120,
            'force_expiry' => false,
            'persistent' => true,
            'cookie' => [
                'name' => 'session',
                'path' => null,
                'domain' => null,
                'secure' => false,
                'http_only' => false
            ]
        ], $config);
    }
}
