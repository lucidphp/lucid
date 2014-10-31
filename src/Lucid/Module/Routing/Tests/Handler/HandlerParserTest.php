<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests\Handler;

use Lucid\Module\Routing\Handler\HandlerParser;
use Lucid\Module\Routing\Tests\Handler\Stubs\SimpleHandler;

/**
 * @class HandlerParserTest
 *
 * @package Lucid\Module\Routing\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HandlerParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldParseStaticHandlers()
    {
        $handlerStr = __CLASS__.'::fakeStaticHandle';
        $foulStr    = __CLASS__.'::foulStaticHandle';

        $parser = new HandlerParser();
        $this->assertInstanceof('Lucid\Module\Routing\Handler\HandlerReflector', $parser->parse($handlerStr));

        try {
            $parser->parse($foulStr);
        } catch (\RuntimeException $e) {
            $this->assertSame('No routing handler could be found.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldParseCallables()
    {
        $parser = new HandlerParser();
        $this->assertInstanceof(
            'Lucid\Module\Routing\Handler\HandlerReflector',
            $parser->parse(
                function () {
                    return true;
                }
            )
        );
    }

    /** @test */
    public function itShouldParseNoneStaticHandlerAnnotation()
    {
        $handlerStr = __NAMESPACE__.'\Stubs\SimpleHandler@noneParamAction';

        $parser = new HandlerParser();

        $handler = $parser->parse($handlerStr);

        $this->assertInstanceof('Lucid\Module\Routing\Handler\HandlerReflector', $handler);
    }

    /** @test */
    public function itShouldParseHandlerAsService()
    {
        $parser = new HandlerParser(['handler' => $this, 'simple_handler' => new SimpleHandler]);

        $this->assertInstanceof(
            'Lucid\Module\Routing\Handler\HandlerReflector',
            $parser->parse('handler@fakeAction')
        );
        $this->assertInstanceof(
            'Lucid\Module\Routing\Handler\HandlerReflector',
            $parser->parse('simple_handler@noneParamAction')
        );
    }

    public static function fakeStaticHandle()
    {
        return true;
    }

    public function fakeAction()
    {
        return true;
    }
}
