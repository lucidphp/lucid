<?php

/*
 * This File is part of the Selene\Adapter\Kernel\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel\Filter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Lucid\Adapter\HttpFoundation\ResponseFilterInterface;

/**
 * @class ExceptionFilter
 *
 * @package Selene\Adapter\Kernel\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResponseExceptionFilter implements ResponseFilterInterface
{
    /**
     * exception
     *
     * @var Exception
     */
    private $exception;

    /**
     * requestedStatus
     *
     * @var string
     */
    private static $requestedStatus = 'X-Status-Code';

    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Response $response)
    {
        if ($status = $this->getRequestedStatusCode($response)) {
            $response->setStatusCode($status);
            $this->removeRequestedStatusCode($response);

            return;
        }

        if (!$this->responseIsError($response) && !$response->isRedirect()) {
            $this->setResponseStatus($response, $this->getException());
        }

        if (0 === strlen($response->getContent())) {
            $response->setContent($this->getException()->getMessage());
        }
    }

    /**
     * getException
     *
     * @return \Exception|null
     */
    protected function getException()
    {
        return $this->exception;
    }

    /**
     * setResponseStatus
     *
     * @return void
     */
    private function setResponseStatus(Response $response, \Exception $exception = null)
    {
        if ($exception instanceof HttpExceptionInterface) {
            $response->headers->add($exception->getHeaders());
            $response->setStatusCode($exception->getStatusCode());
        } else {
            $response->setStatusCode(500);
        }
    }

    /**
     * responseIsError
     *
     * @param Response $response
     *
     * @return boolean
     */
    private function responseIsError(Response $response)
    {
        return $response->isClientError() || $response->isServerError();
    }

    /**
     * getRequestedStatusCode
     *
     * @param Response $response
     *
     * @return void
     */
    private function getRequestedStatusCode(Response $response)
    {
        if ($response->headers->has(self::$requestedStatus)) {
            return $response->headers->get(self::$requestedStatus);
        }

        return false;
    }

    /**
     * removeRequestedStatusCode
     *
     * @param Response $response
     *
     * @return void
     */
    private function removeRequestedStatusCode(Response $response)
    {
        $response->headers->remove(self::$requestedStatus);
    }
}
