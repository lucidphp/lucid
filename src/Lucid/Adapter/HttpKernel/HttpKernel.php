<?php

/*
 * This File is part of the Lucid\Adapter\HttpKernel package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Lucid\Event\EventDispatcherInterface;
use Lucid\Adapter\HttpKernel\Event\KernelEvents as Events;
use Lucid\Adapter\HttpKernel\Event\RequestEvent;
use Lucid\Adapter\HttpKernel\Event\ResponseEvent;
use Lucid\Adapter\HttpKernel\Event\ExceptionEvent;
use Lucid\Adapter\HttpKernel\Filter\ResponseExceptionFilter;
use Lucid\Adapter\HttpFoundation\RequestStack;
use Lucid\Adapter\HttpFoundation\RequestStackInterface;

/**
 * @class HttpKernel
 *
 * @package Lucid\Adapter\HttpKernel
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HttpKernel implements HttpKernelInterface
{
    private $events;
    private $requests;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param RequestStackInterface $requests
     */
    public function __construct(EventDispatcherInterface $events, RequestStackInterface $requests = null)
    {
        $this->events = $events;
        $this->requests = $requests ?: new RequestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->requests->push($request);

        try {
            $response = $this->handleRequest($request, $type, $catch);
        } catch (\Exception $e) {

            if ($catch) {
                $response = $this->handleRequestException($e, $request, $type);
            } else {
                throw $e;
            }
        }

        $this->requests->pop($request);

        return $this->filterResponse($response);
    }

    /**
     * filterResponse
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function filterResponse(Response $response)
    {
        $event = new ResponseEvent;
        $event->setResponse($response);

        $event->setName(Events::ON_RESPONSE);
        $this->events->dispatchEvent($event);

        return $event->getResponse();
    }

    /**
     * handleRequest
     *
     * @param Request $request
     * @param int $type
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    protected function handleRequest(Request $request, $type)
    {
        $event = new RequestEvent($request, $type);
        $event->setName(Events::ON_REQUEST);

        $this->events->dispatchEvent($event);

        if ($response = $event->getResponse()) {

            $rEvent = clone $event;
            $rEvent->setName(Events::ON_RESPONSE);
            $this->events->dispatchEvent($rEvent);

            return $rEvent->getResponse() ?: $response;
        }

        throw new NotFoundHttpException(
            sprintf('Requested resource for %s was not found.', $request->getRequestUri())
        );
    }

    /**
     * handleRequestException
     *
     * @param \Exception $e
     * @param Request $request
     * @param int $type
     *
     * @return Response
     */
    protected function handleRequestException(\Exception $e, Request $request, $type)
    {
        $event = new ExceptionEvent($e, $request, $type);
        $event->setName(Events::ON_EXCEPTION);

        $this->events->dispatchEvent($event);

        if (!$response = $event->getResponse()) {
            $response = new Response($response);

            $filter = new ResponseExceptionFilter($e);
            $filter->filter($response);
        }

        return $response;
    }
}
