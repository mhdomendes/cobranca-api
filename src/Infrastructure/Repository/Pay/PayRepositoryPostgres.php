<?php

namespace Infrastructure\Repository\Pay;

use Domain\Pay\Entity\Payment;
use Domain\Pay\Repository\PayRepositoryInterface;
use Infrastructure\Database\Connection;

class PayRepositoryPostgres implements PayRepositoryInterface
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getConnection();
    }

    public function save(Payment $payment): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO payments (debt_id, amount, paid_at)
            VALUES (:debt_id, :amount, :paid_at)
        ");

        $stmt->execute([
            'debt_id' => $payment->getDebtId(),
            'amount' => $payment->getAmount(),
            'paid_at' => $payment->getPaidAt()->format('Y-m-d H:i:s')
        ]);

        $id = $this->connection->lastInsertId();

        if ($id) {
            $reflection = new \ReflectionClass($payment);
            $prop = $reflection->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($payment, (int) $id);
        }
    }

    public function findById(int $id): ?Payment
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM payments WHERE id = :id
        ");

        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToEntity($data);
    }

    public function findByDebtId(int $debtId): array
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM payments WHERE debt_id = :debt_id
        ");

        $stmt->execute(['debt_id' => $debtId]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToEntity($row), $rows);
    }

    private function mapToEntity(array $data): Payment
    {
        return new Payment(
            $data['id'],
            (int) $data['debt_id'],
            (float) $data['amount'],
            new \DateTime($data['paid_at'])
        );
    }
}