<?php

namespace app\Domain;

/**
 * Interface AuthenticatorInterface
 *
 * @package app\Domain
 */
interface AuthenticatorInterface
{
    /**
     * Говорит, есть ли пользователь с таким токеном
     *
     * @param string $token
     *
     * @return boolean
     */
    public function isValidToken(string $token);
}