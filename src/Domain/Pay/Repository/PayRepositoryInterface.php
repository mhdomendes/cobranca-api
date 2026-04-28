<?php

namespace Domain\Pay\Repository;

use Domain\Pay\Entity\Payment;

interface PayRepositoryInterface
{
    public function save(Payment $payment): void;

    public function findById(int $id): ?Payment;

    /** @return Payment[] */
    public function findByDebtId(int $debtId): array;
}