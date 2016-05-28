<?php
/**
 * This File is part of the lucid package
 *  
 *  (c) malcolm <$email>
 *  
 *  For full copyright and license information, please refer to the LICENSE file
 *  that was distributed with this package.
 */

namespace Lucid\Common\Tests\Struct\Stubs;

/**
 * Created by PhpStorm.
 * User: malcolm
 * Date: 27.05.16
 * Time: 21:30
 */
class IntegerCollection extends \Lucid\Common\Struct\AbstractCollection
{
    /** @var  array */
    private $data;
    
    protected function setData(int ...$args) {
        $this->data = $args;
    }

    protected function getData() : array
    {
        return $this->data;
    }

    protected function getSetterMethod() : string
    {
        return 'setData';
    }
}