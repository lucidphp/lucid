<?php

/*
 * This File is part of the Lucid\Http\Session\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Handler;

use Mongo;
use MongoClient;
use InvalidArgumentException;

/**
 * @class MongoSessionHandler
 *
 * @package Lucid\Http\Session\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MongoSessionHandler extends AbstractSessionHandler
{
    private $options;
    private $collection;
    private $fId;
    private $fData;
    private $fTime;

    public function __construct($client, array $options)
    {
        $this->setOptions($this->prepareOptions($options));
        $this->setClient($client);

        parent::__construct($options['ttl'], $options['prefix']);
    }

    public function write($sessionId, $data)
    {
        $this->getCollection();
    }

    protected function getClient()
    {
        return $this->client;
    }

    private function setOptions(array $options)
    {
        $this->fId   = $options['field_id'];
        $this->fData = $options['field_data'];
        $this->fTime = $options['field_time'];

        $this->options = $options;
    }

    private function prepareOptions(array $options)
    {
        if (!isset($options['database']) || !isset($options['collection'])) {
            throw new InvalidArgumentException;
        }

        return array_merge([
            'ttl' => 60,
            'prefix' => self::DEFAULT_PREFIX,
            'field_id' => '_id',
            'field_data' => 'data',
            'field_time' => 'time',
            'field_ttl'  => 'ttl',
        ], $options);
    }

    /**
     * setClient
     *
     * @param mixed $client
     *
     * @return void
     */
    private function setClient($client)
    {
        if (!$client instanceof Mongo && !$client instanceof MongoClient) {
            throw new InvalidArgumentException;
        }

        $this->client = $client;
        $this->client->selectDB($this->options['database']);
    }

    private function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->client->selectCollection(
                $this->options['database'],
                $this->options['collection']
            );
        }

        return $this->collection;
    }
}
