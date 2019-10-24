<?php

namespace App\Domain\Storage;

interface UserRelConnectionStorageInterface
{
    /**
     * @param int $connectionId
     * @param int $userId
     */
    public function addRelation(int $connectionId, int $userId);

    /**
     * @param int $connectionId
     */
    public function removeRelation(int $connectionId);

    /**
     * @param int $connectionId
     * @return array|null
     */
    public function findUserByConnectionId(int $connectionId);
}