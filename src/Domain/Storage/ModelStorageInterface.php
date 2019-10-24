<?php

namespace app\Domain\Storage;

use app\Domain\Model\ModelInterface;

interface ModelStorageInterface
{
    /**
     * @param string $modelName
     * @param int    $id
     *
     * @return ModelInterface
     */
    public function getModelInstance(string $modelName, int $id): ModelInterface;

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model);
}