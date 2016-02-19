<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package;

use Ludic\Signal\EventDispatcherInterface as Events;

/**
 * @class LoggerAwarePublisher
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventAwarePublisher extends Publisher
{
    /** @var Logger */
    private $events;

    /**
     * Sets a PSR compatible logger.
     *
     * @param Logger $logger
     *
     * @return void
     */
    public function setEvents(Events $events)
    {
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    protected function publishFiles(ProviderInterface $package, $target = null, $override = false)
    {
        $this->notifyPublish($package);

        try {
            $ret = parent::publishFiles($package, $target, $override);
        } catch (PublishException $e) {
            $this->notifyPublishException($package, $e);

            return self::NOT_PUBLISHED;
        }

        $this->notifyNotPublished($package, $file->getSource());

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    protected function publishFile($file, $path, $override)
    {
        $ret = parent::publishFile($file, $path, $override);

        if ($ret) {
        } else {
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    protected function publishDefault(ProviderInterface $package, $target = null, $override = false, $force = false)
    {
        if (self::NOT_PUBLISHED === ($ret = parent::publishDefault($package, $target, $override, $force))) {
            $this->notifyNotPublished($package, $file);
        }
    }

    /**
     * notifyPublish
     *
     * @param mixed $package
     *
     * @return void
     */
    protected function notifyPublish(ProviderInterface $package)
    {
        if ($this->events) {
            $this->events->dispatch(PublishEvents::EVENT_PUBLISH_PACKAGE, new PackageEvent($package));
        }
    }

    /**
     * notifyPublished
     *
     * @param mixed $package
     * @param mixed $file
     *
     * @return void
     */
    protected function notifyPublished(ProviderInterface $package, $file)
    {
        if ($this->events) {
            $this->events->dispatch(PublishEvents::EVENT_PUBLISHED, new PackagePublishEvent($package, $file));
        }
    }

    /**
     * @param ProviderInterface $package
     * @param \Exception $e
     *
     * @access protected
     * @return mixed
     */
    protected function notifyPublishException(ProviderInterface $package, \Exception $e)
    {
        if ($this->events) {
            $this->events->dispatch(PublishEvents::EVENT_PUBLISH_EXCEPTION, new PackageExceptionEvent($package, $e));
        }
    }

    /**
     * notifyPublished
     *
     * @param mixed $package
     * @param mixed $file
     *
     * @return void
     */
    protected function notifyNotPublished(ProviderInterface $package, $file)
    {
        if ($this->events) {
            $this->events->dispatch(PublishEvents::EVENT_NOT_PUBLISHED, new PackagePublishEvent($package, $file));
        }
    }
}
