<?php

namespace app\Storage;

use app\Domain\Storage\ManagerStorageInterface;

/**
 * Class ManagerStorage
 *
 * @package app\Storage
 */
class ManagerStorage implements ManagerStorageInterface
{
    private $managers;

    /**
     * @var int[][]
     */
    private $managerRelConnections;

    /**
     * @inheritDoc
     */
    public function addManagerConnection(int $managerId, int $connectionId)
    {
        if (!isset($this->managerRelConnections[$managerId])) {
            $this->managerRelConnections[$managerId] = [];
        }
        if (false === array_search($connectionId, $this->managerRelConnections[$managerId], true)) {
            $this->managerRelConnections[$managerId][] = $connectionId;
        }
    }

    /**
     * @inheritDoc
     */
    public function setManagerStatus(int $managerId, string $status)
    {

    }
}
