<?php
/**
 * This File is part of the lucid package
 *
 *  (c) malcolm <$email>
 *
 *  For full copyright and license information, please refer to the LICENSE file
 *  that was distributed with this package.
 */

namespace Lucid\Mux\Meta;

class Attributes implements AttributesInterface
{
    /** * @var array */
    private $attrs = [];

    public function get($attr) /*: mixed | null*/
    {
        return $this->attrs[$attr] ?? null;
    }

    public function add($attr, /*scalar*/ $value)
    {
        $this->attrs[$attr] = $value;
    }
}