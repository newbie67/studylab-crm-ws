<?php

namespace app\Component;

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
        return [];
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