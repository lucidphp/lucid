<?php

/*
 * This File is part of the Lucid\Adapter\HttpKernel\Subscriber package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel\Subscriber;

use Lucid\Event\SubscriberInterface;
use Lucid\Routing\RouterInterface;
use Lucid\Adapter\HttpKernel\Event\KernelEvents as Events;
use Lucid\Adapter\HttpKernel\Event\RequestEvent;
use Lucid\Routing\Http\RequestContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * @class RoutingSubscriber
 *
 * @package Lucid\Adapter\HttpKernel\Subscriber
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RoutingSubscriber implements SubscriberInterface
{
    /**
     * router
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     *
     * @return void
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriptions()
    {
        return [Events::ON_REQUEST => ['onKernelRequest', 1000]];
    }

    /**
     * Dispatches a request context on the router object.
     *
     * @param RequestEvent $event
     * @throws NotFoundHttpException $event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        try {
            $response = $this->router->dispatch(
                $context = RequestContext::fromSymfonyRequest($event->getRequest())
            );
        } catch (MatchException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $event->setResponse(
            $response instanceof Response ? $response : new Response($response, 200)
        );

        $event->stop();
    }
}
