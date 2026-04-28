<?php

namespace Domain\Debt\Repository;

use Domain\Debt\Entity\Debt;
use Domain\Debt\ValueObject\Cpf;

interface DebtRepositoryInterface
{
    public function save(Debt $debt): void;

    public function findById(int $id): ?Debt;

    public function findByCpf(Cpf $cpf): ?Debt;

    public function update(Debt $debt): void;
}