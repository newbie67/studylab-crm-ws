<?php

namespace app\Domain\Storage;

/**
 * Interface StorageInterface
 *
 * @package app\Domain\Storage
 */
interface StorageInterface
{
    /**
     * @return ConnectionStorageInterface
     */
    public function getConnectionStorage() : ConnectionStorageInterface;

    /**
     * @return ManagerStorageInterface
     */
    public function getManagerStorage() : ManagerStorageInterface;
}