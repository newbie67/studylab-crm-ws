<?php

namespace app\Domain\Component;

/**
 * Interface CrmInterface
 *
 * @package app\Domain
 */
interface CrmInterface
{
    /**
     * Говорит, есть ли пользователь с таким токеном
     *
     * @param int    $id
     * @param string $token
     *
     * @return boolean
     */
    public function isValidToken(int $id, string $token);

    /**
     * @return array[]
     */
    public function getManagers();
}