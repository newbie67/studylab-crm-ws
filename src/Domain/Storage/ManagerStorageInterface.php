<?php

namespace app\Domain\Storage;

/**
 * Interface ManagerStorageInterface
 *
 * @package app\Domain\Storage
 */
interface ManagerStorageInterface
{
    /**
     * Добавляет текущий коннек
     *
     * @param int $managerId
     * @param int $connectionId
     */
    public function addManagerConnection(int $managerId, int $connectionId);

    /**
     * @param int    $managerId
     * @param string $status
     */
    public function setManagerStatus(int $managerId, string $status);
}