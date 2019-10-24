<?php

namespace App\Storage;

use App\Domain\Service\CrmInterface;
use App\Domain\Storage\UserRelConnectionStorageInterface;

class UserRelConnectionStorage implements UserRelConnectionStorageInterface
{
    /**
     * @var array
     */
    private $connectionRelUsers = [];

    /**
     * @var CrmInterface
     */
    private $crm;

    /**
     * UserRelConnectionStorage constructor.
     *
     * @param CrmInterface $crm
     */
    public function __construct(CrmInterface $crm)
    {
        $this->crm = $crm;
    }

    /**
     * @inheritDoc
     */
    public function addRelation(int $connectionId, int $userId)
    {
        $this->connectionRelUsers[$connectionId] = $userId;
    }

    /**
     * @inheritDoc
     */
    public function removeRelation(int $connectionId)
    {
        unset($this->connectionRelUsers[$connectionId]);
    }

    /**
     * @inheritDoc
     */
    public function findUserByConnectionId(int $connectionId)
    {
        if (false === array_key_exists($connectionId, $this->connectionRelUsers)) {
            return null;
        }
        $userId = $this->connectionRelUsers[$connectionId];
        if (array_key_exists($userId, $this->crm->getAllUsers())) {
            return $this->crm->getAllUsers()[$userId];
        }

        return null;
    }
}