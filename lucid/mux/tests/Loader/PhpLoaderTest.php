<?php declare(strict_types=1);

namespace Lucid\Mux\Tests\Loader;

use Lucid\Mux\Routes;
use Lucid\Resource\Collection;
use Lucid\Mux\Loader\PhpLoader;
use Lucid\Resource\Loader\Resolver;
use Lucid\Mux\RouteCollectionBuilder;

class PhpLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldReturnRoutes()
    {
        $loader = new PhpLoader(null, $this->mockLocator());

        $this->assertInstanceOf('Lucid\Mux\RouteCollectionInterface', $routes = $loader->loadRoutes('routes.php'));
    }

    /** @test */
    public function itShouldReturnInputCollection()
    {
        $loader = new PhpLoader(new RouteCollectionBuilder($routes = new Routes), $this->mockLocator());

        $this->assertSame($routes, $loader->loadRoutes('routes.php'));
    }

    /** @test */
    public function itShouldImportRoutes()
    {
        $loader = new PhpLoader($builder = new RouteCollectionBuilder, $loc = $this->mockLocator());

        $mockedLoader = $this->getMockbuilder('Lucid\Mux\Loader\PhpLoader')
            ->setMethods(['getExtensions'])
            ->setConstructorArgs([$builder, $loc])->getMock();
        $mockedLoader->method('getExtensions')->willReturn(['routes']);

        $resolver = new Resolver([$loader, $mockedLoader]);

        $routes = $loader->loadRoutes('route_imports.php');


        $all = $routes->all();

        $this->assertArrayHasKey('foo', $all);
        $this->assertArrayHasKey('bar', $all);

        $this->assertSame('/admin/foo', $routes->get('foo')->getPattern());
        $this->assertSame('/admin/bar', $routes->get('bar')->getPattern());
    }

    /** @test */
    public function itShouldLoadRoutes()
    {
        $loader = new PhpLoader(null, $this->mockLocator());
        $routes = $loader->loadRoutes('routes.0.php');

        $this->assertTrue($routes->has('index'));
    }

    /** @test */
    public function itShouldLoadGroups()
    {
        $loader = new PhpLoader(null, $this->mockLocator());
        $routes = $loader->loadRoutes('route_groups.0.php');

        $this->assertTrue($routes->has('users'));
        $this->assertTrue($routes->has('affiliates'));

        $this->assertSame('example.com', $routes->get('users')->getHost());
        $this->assertSame(['https'], $routes->get('affiliates')->getSchemes());

        $this->assertSame('example.com', $routes->get('affiliates')->getHost());
        $this->assertSame(['https'], $routes->get('users')->getSchemes());

        $loader = new PhpLoader(null, $this->mockLocator());
        $routes = $loader->loadRoutes('route_groups.1.php');

        $this->assertNull($routes->get('users')->getHost());
        $this->assertSame(['http', 'https'], $routes->get('users')->getSchemes());

        $loader = new PhpLoader(null, $this->mockLocator());
        $routes = $loader->loadRoutes('route_groups.2.php');

        $this->assertNull($routes->get('users')->getHost());
        $this->assertSame(['http', 'https'], $routes->get('users')->getSchemes());
    }

    /** @test */
    public function itShouldThrowOnInvalidConfig()
    {
        $loader = new PhpLoader(null, $this->mockLocator());

        try {
            $loader->loadRoutes('faulty.php');
        } catch (\Lucid\Resource\Exception\LoaderException $e) {
            $this->assertEquals('Return value must be an array.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    private function mockLocator()
    {
        $locator = $this->getMockbuilder('Lucid\Resource\LocatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $locator->method('locate')->willReturnCallback(function ($file) {
            $collection = new Collection;

            if (file_exists($file = $this->fixure($file))) {
                $collection->addFileResource($file);
            }

            return $collection;
        });

        return $locator;
    }

    private function fixure($file)
    {
        $prefix = __DIR__.DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR;

        return $prefix.$file;
    }
}
