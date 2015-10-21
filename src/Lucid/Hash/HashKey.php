<?php

/*
 * This File is part of the Lucid\Hash package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Hash;

use Lucid\Common\Helper\Str;
use Lucid\Hash\Helper\BcConvertHelper;

/**
 * @class HashKey
 * @see HashInterface
 *
 * @package Selene\Module\Cryptography
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class HashKey implements HashInterface
{
    /**
     * hash_hmac key
     *
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key = null)
    {
        $this->key = $key;
    }

    /**
     * hash
     *
     * @param mixed $value
     * @param array $options
     *
     * @access public
     * @return string
     */
    public function hash($value, array $options = null)
    {
        $options = $this->getOptions($options ?: []);

        return $this->generate($value, $options);
    }

    /**
     * check
     *
     * @param mixed $value
     * @param mixed $hash
     * @param mixed $options
     *
     * @access public
     * @return bool
     */
    public function check($input, $hash, $options = null)
    {
        return Str::safeCmp($hash, $this->hash($input, $options));
    }

    /**
     * generate
     *
     * @param mixed $value
     * @param mixed $options
     *
     * @return string
     */
    private function generate($value, $options)
    {
        $base = hash_hmac('md5', $value, $options['secret']);

        if (!function_exists('gmp_init')) {
            return BcConvertHelper::baseConvert($base, 16, 62);
        }

        $init = gmp_init($base, 16);

        return  gmp_strval($init, 62);
    }

    /**
     * getOptions
     *
     * @param array $options
     *
     * @access private
     * @return array
     */
    private function getOptions(array $options = [])
    {
        if (!isset($options['secret'])) {
            $options['secret'] = $this->key;
        }

        return $options;
    }
}
