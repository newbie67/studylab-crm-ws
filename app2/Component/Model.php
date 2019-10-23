<?php

namespace app2\Component;

use Workerman\Connection\TcpConnection;

class Model
{
    /**
     * @var self[][]
     */
    private static $instances = [];

    /**
     * @var string
     */
    private $modelName;

    /**
     * @var int
     */
    private $id;

    /**
     * @var array[]
     */
    private $lockedFields = [];

    /**
     * @param string $modelName
     * @param int    $id
     *
     * @return Model
     */
    public static function getInstance(string $modelName, int $id): self
    {
        if (false === array_key_exists($modelName, self::$instances)) {
            self::$instances[$modelName] = [];
        }

        if (false === array_key_exists($id, self::$instances[$modelName])) {
            self::$instances[$modelName][$id] = new self($modelName, $id);
        }

        return self::$instances[$modelName][$id];
    }

    /**
     * @return string[]
     */
    public function getLockedFields(): array
    {
        return $this->lockedFields;
    }

    /**
     * @param TcpConnection $connection
     * @param array         $fields
     */
    public function lockFields(TcpConnection $connection, array $fields)
    {
        foreach ($fields as $field) {
            $this->lockedFields[$field] = $connection->id;
        }
    }

    /**
     * @param string[] $fields
     */
    public function unlockFields(array $fields)
    {
        foreach ($fields as $field) {
            unset($this->lockedFields[$field]);
        }
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $modelName
     * @param int    $id
     */
    private function __construct(string $modelName, int $id)
    {
        $this->modelName = $modelName;
        $this->id = $id;
    }
}