<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use Lucid\Writer\FormatterHelper;
use Lucid\Writer\Object\ClassWriter;
use Lucid\Writer\Object\Method;
use Lucid\Writer\Object\Argument;
use Lucid\Writer\Object\Property;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Parser\ParserInterface as Ps;

/**
 * @class Dumper
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Dumper
{
    use FormatterHelper;

    /** @var string */
    const NGRP_RPLC = '%1$s(\(\?P\<)(.*?)(\>)%1$s';

    /** @var string */
    private $className;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct($class = 'Lucid\Mux\Cache\Matcher\CachedMatcher')
    {
        $this->className = $class;
    }

    /**
     * dump
     *
     * @param RouteCollectionInterface $routes
     *
     * @return string
     */
    public function dump(RouteCollectionInterface $routes)
    {
        $map = $this->createMap($routes);
        $obj = new ClassWriter($this->className, null, 'Lucid\Mux\Cache\Matcher\FastMatcher');
        $obj->addProperty($pmap = new Property('map', Property::IS_PROTECTED, false));

        $pmap->setValue($this->extractParams($map));

        $obj->addMethod($match = new Method('__construct'));
        $obj->addUseStatement('Lucid\Mux\RouteCollectionInterface');
        $obj->addUseStatement('Lucid\Mux\Request\ContextInterface');

        return $obj->generate();
    }

    /**
     * createMap
     *
     * @param RouteCollectionInterface $routes
     *
     * @return array
     */
    public function createMap(RouteCollectionInterface $routes)
    {
        $i    = 0;
        $m    = [];
        $rpl  = sprintf(self::NGRP_RPLC, Ps::EXP_DELIM);
        $expr = [];
        $pfx  = 'r'.time();
        $expr['prefix'] = $pfx;

        foreach ($routes->all() as $name => $route) {
            foreach ($route->getMethods() as $method) {
                $m[$method][$name] = $route;
            }
        }

        foreach ($m as $mn => $rts) {
            $mn = strtolower($mn);
            $expr[$mn]['map'] = [];
            $regex = [];

            foreach ($rts as $rn => $rt) {
                $ctx      = $rt->getContext();
                $index    = $pfx.'_'.$this->replaceRouteName($rn, $i);
                $ctxRegex = preg_replace($rpl, '$1r'.(string)$i.'_$2>', $ctx->getRegex(true));
                $regex[]  = sprintf('(?P<%s>%s)', $index, $ctxRegex);
                $expr[$mn]['map'][$index] = [$i, $rn, 'r'.$i.'_'];
                $i++;
            }

            $i = 0;
            $expr[$mn]['regex'] = sprintf('%1$s(?:^'.join('|', $regex).')$%1$sx', Ps::EXP_DELIM);
        }

        return $expr;
    }

    /**
     * replaceRouteName
     *
     * @param mixed $name
     * @param mixed $i
     *
     * @return string
     */
    private function replaceRouteName($name, $i)
    {
        return (string)$i.'_'.preg_replace('~[^\w+]~', '_', $name);
    }
}
