<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Storage;

use Lucid\Mux\Cache\CachedCollectionInterface;
use Lucid\Mux\RouteCollectionInterface;
use PDO as DB;
use Lucid\Mux\Cache\StorageInterface;

/**
 * Class PDO
 * @package Lucid\Mux\Cache\Storage
 */
abstract class PDO implements StorageInterface
{
    use StorageTrait;

    /** @var \PDO  */
    private $pdo;

    /** @var string */
    private $table;

    /** @var bool */
    protected $exists;

    /**
     * PDO constructor.
     *
     * @param \PDO $pdo
     * @param string $tableName
     */
    public function __construct(DB $pdo, string $tableName)
    {
        $this->storeId = self::DEFAULT_PREFIX;
        $this->pdo = $pdo;
        $this->table = $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function read(): ?CachedCollectionInterface
    {
        // TODO: Implement read() method.
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes): void
    {
        // TODO: Implement write() method.
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(int $time) : bool
    {
        // TODO: Implement isValid() method.
    }

    /**
     * {@inheritdoc}
     */
    public function exists() : bool
    {
        if ($this->exists !== null) {
            return $this->exists;
        }

        try {
            $this->fetchCollection();
        } catch (\PDOException $e) {
            return $this->exists;
        }

        return $this->exists = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime() : int
    {
        if (!$this->exists()) {
            return time();
        }

        return $this->fetchCollection()->getTimestamp();
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    protected function table() : string
    {
        return $this->table;
    }

    /**
     * @return \PDO
     */
    protected function pdo() : DB
    {
        return $this->pdo;
    }

    /**
     * @return array
     */
    abstract protected function createScheme() : array;

    abstract protected function fetchCollection() : CachedCollectionInterface;
}