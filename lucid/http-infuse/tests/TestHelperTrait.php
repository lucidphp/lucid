<?php

/*
 * This File is part of the Lucid\Http\Infuse\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Infuse\Tests;

/**
 * @trait TestHelperTrait
 *
 * @package Lucid\Http\Infuse\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait TestHelperTrait
{
    private function mockHttpDispatcher()
    {
        return $this->getMockbuilder('Lucid\Http\Core\DispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockRequest()
    {
        return $this->getMockbuilder('Psr\Http\Message\ServerRequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockResponse()
    {
        return $this->getMockbuilder('Psr\Http\Message\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
