<?php

namespace App\Domain\Model;

use Workerman\Connection\TcpConnection;

interface ModelInterface
{
    /**
     * @param int    $id
     * @param string $modelName
     *
     * @return ModelInterface
     */
    public static function getInstance(int $id, string $modelName): ModelInterface;

//    /**
//     * @return FieldInterface[]
//     */
//    public function getLockedFields(): array;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param TcpConnection $connection
     */
    public function addEditBy(TcpConnection $connection);

    /**
     * @return TcpConnection[]
     */
    public function getEditedBy();

    /**
     * @param TcpConnection $connection
     */
    public function removeEditBy(TcpConnection $connection);

    /**
     * @param string $fieldName
     *
     * @return FieldInterface
     */
    public function getField(string $fieldName);

    /**
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * @param string $fieldName
     */
    public function unlockField(string $fieldName);

    /**
     * @param TcpConnection $connection
     * @param string $fieldName
     */
    public function lockField(TcpConnection $connection, string $fieldName);
}