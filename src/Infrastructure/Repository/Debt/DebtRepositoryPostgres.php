<?php

namespace Infrastructure\Repository\Debt;

use Domain\Debt\Entity\Debt;
use Domain\Debt\Repository\DebtRepositoryInterface;
use Domain\Debt\ValueObject\Cpf;
use Infrastructure\Database\Connection;

class DebtRepositoryPostgres implements DebtRepositoryInterface
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getConnection();
    }

    public function save(Debt $debt): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO debts (cpf, amount, status, data_vencimento)
            VALUES (:cpf, :amount, :status, :data_vencimento)
        ");

        $stmt->execute([
            'cpf' => (string) $debt->getCpf(),
            'amount' => $debt->getValor(),
            'status' => $debt->getStatus(),
            'data_vencimento' => $debt->getDataVencimento()->format('Y-m-d')
        ]);
        
        $id = $this->connection->lastInsertId();

        if ($id) {
            $reflection = new \ReflectionClass($debt);
            $prop = $reflection->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($debt, (int) $id);
        }
    }

    public function findById(int $id): ?Debt
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM debts WHERE id = :id
        ");

        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToEntity($data);
    }

    public function findByCpf(Cpf $cpf): ?Debt
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM debts 
            WHERE cpf = :cpf AND status != 'PAID'
            LIMIT 1
        ");

        $stmt->execute(['cpf' => (string) $cpf]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToEntity($data);
    }

    public function update(Debt $debt): void
    {
        $stmt = $this->connection->prepare("
            UPDATE debts
            SET amount = :amount,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $debt->getId(),
            'amount' => $debt->getValor(),
            'status' => $debt->getStatus()
        ]);
    }

    private function mapToEntity(array $data): Debt
    {
        return new Debt(
            $data['id'],
            new Cpf($data['cpf']),
            (float) $data['amount'],
            $data['status'],
            new \DateTime($data['data_vencimento'])
        );
    }
}