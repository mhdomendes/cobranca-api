<?php

namespace Domain\Collect\Repository;

use Domain\Collect\Entity\Collect;
use Domain\Debt\ValueObject\Cpf;

interface CollectRepositoryInterface
{
    public function save(Collect $collect): void;

    public function findById(int $id): ?Collect;

    public function findByCpf(Cpf $cpf): array;

    public function update(Collect $collect): void;
}