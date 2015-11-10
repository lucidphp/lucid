<?php

/*
 * This File is part of the Lucid\Cache\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Driver;

use PDO;

/**
 * @class AbstractSqlDriver
 *
 * @package Lucid\\Cache\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AbstractSqlDriver implements DirverInterface
{
    /**
     * client
     *
     * @var PDO
     */
    private $client;

    /**
     * Constructor.
     *
     * @param PDO $client
     *
     * @return void
     */
    public function __construct(PDO $client)
    {
        $this->client = $client;
    }

    /**
     * client
     *
     *
     * @return PDO
     */
    final protected function client()
    {
        return $this->client;
    }
}
