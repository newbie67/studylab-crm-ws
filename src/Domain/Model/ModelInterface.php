<?php

namespace app\Domain\Model;

interface ModelInterface
{
    /**
     * @return FieldInterface[]
     */
    public function getLockedFields(): array;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;
}