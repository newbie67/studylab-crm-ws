<?php

namespace app\Domain\Model;

use Workerman\Connection\TcpConnection;

interface FieldInterface
{
    public function getName(): string;
    public function getValue(): string;
    public function lock();
    public function unlock();

    /**
     * @param TcpConnection $connection
     *
     * @return bool
     */
    public function isLockedBy(TcpConnection $connection): bool;
}