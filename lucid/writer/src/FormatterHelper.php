<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer;

use Lucid\Common\Helper\Arr;

/**
 * @trait FormatterHelper
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait FormatterHelper
{
    /**
     * indent
     *
     * @param int $count
     *
     * @access public
     * @return string
     */
    public function indent($count = 4)
    {
        return $count <= 0 ? '' : str_repeat(' ', (int)$count);
    }

    /**
     * extractParams
     *
     * @param array $params
     * @param int $indent
     *
     * @access protected
     * @return string
     */
    public function extractParams(array $params, $indent = 0)
    {
        $indent = $indent + 4;
        $result = $this->doExctractParams($params, $indent);

        return $this->indent(max($indent - 4, 0)) . $result;
    }

    /**
     * doExctractParams
     *
     * @param array $params
     * @param int $indend
     *
     * @return string
     */
    private function doExctractParams(array $params, $indent = 0)
    {
        $array = [];

        foreach ($params as $param => $value) {
            if (is_array($value)) {
                $value = $this->doExctractParams($value, $indent + 4);
            } elseif (is_string($value) && 0 === strpos($value, '$this')) {
                $value = $value;
            } else {
                $value = $this->exportVar($value);
            }

            $key = $this->exportVar($param);
            $array[$key] = sprintf('%s%s => %s,', $this->indent($indent), $key, $value);
        }

        if (empty($array)) {
            return '[]';
        }

        $flat = sprintf("[\n%s\n%s]", implode("\n", $array), $this->indent(max($indent - 4, 0)));

        if (Arr::isList($array, true)) {
            return preg_replace('#\d+ \=\>\s?#i', '', $flat);
        }

        return $flat;
    }

    /**
     * dumpExport
     *
     * @param mixed $param
     *
     * @access public
     * @return string
     */
    public function exportVar($param)
    {
        return preg_replace(['~NULL~', '~FALSE~', '~TRUE~'], ['null', 'false', 'true'], var_export($param, true));
    }
}
