<?php

namespace App\Domain\Service;

interface CrmInterface
{
    /**
     * @param int    $managerId
     * @param string $token
     *
     * @return bool
     */
    public function isValidToken(int $managerId, string $token): bool;

    /**
     * Return all users (managers + admins + secretaries etc.)
     *
     * @return array
     */
    public function getAllUsers(): array;

    /**
     * Returns only managers (without other users)
     *
     * @return array
     */
    public function getManagers(): array;
}