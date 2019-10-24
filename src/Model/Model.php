<?php
namespace App\Model;

use App\Domain\Model\FieldInterface;
use App\Domain\Model\ModelInterface;
use Workerman\Connection\TcpConnection;

class Model implements ModelInterface
{
    /**
     * @var ModelInterface[]
     */
    private static $instances = [];

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var TcpConnection[]
     */
    private $editedBy = [];

    /**
     * @var FieldInterface[]
     */
    private $fields = [];

    /**
     * @inheritDoc
     */
    public static function getInstance(int $id, string $name): ModelInterface
    {
        $key = $id . '|' . $name;
        if (false === array_key_exists($key, self::$instances)) {
            self::$instances[$key] = new self($id, $name);
        }
        return self::$instances[$key];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function lockField(TcpConnection $connection, string $fieldName)
    {
        if (false === array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName] = new Field($connection, $fieldName);
        };
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function addEditBy(TcpConnection $connection)
    {
        $this->editedBy[$connection->id] = $connection;
    }

    /**
     * @inheritDoc
     */
    public function getEditedBy()
    {
        return $this->editedBy;
    }

    /**
     * @inheritDoc
     */
    public function removeEditBy(TcpConnection $connection)
    {
        unset($this->editedBy[$connection->id]);
    }

    /**
     * @inheritDoc
     */
    public function getField(string $fieldName)
    {
        return $this->fields[$fieldName];
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    public function unlockField(string $fieldName)
    {
        unset($this->fields[$fieldName]);
    }

    /**
     * Model constructor.
     *
     * @param int    $id
     * @param string $name
     */
    private function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
