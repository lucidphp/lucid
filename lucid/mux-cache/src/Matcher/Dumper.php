<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Matcher;

use Lucid\Writer\FormatterHelper;
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

        return sprintf("<?php\n\nreturn %s;", $this->extractParams($map));
    }

    /**
     * createMap
     *
     * @param RouteCollectionInterface $routes
     *
     * @return array
     */
    private function createMap(RouteCollectionInterface $routes)
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
