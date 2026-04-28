<?php

namespace Infrastructure\Repository\Collect;

use Domain\Collect\Entity\Collect;
use Domain\Collect\Repository\CollectRepositoryInterface;
use Domain\Debt\ValueObject\Cpf;
use Infrastructure\Database\Connection;

class CollectRepositoryPostgres implements CollectRepositoryInterface
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getConnection();
    }

    public function save(Collect $collect): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO collects (cpf, amount, data_vencimento, status)
            VALUES (:cpf, :amount, :data_vencimento, :status)
        ");

        $stmt->execute([
            'cpf' => (string) $collect->getCpf(),
            'amount' => $collect->getAmount(),
            'data_vencimento' => $collect->getDataVencimento()->format('Y-m-d'),
            'status' => $collect->getStatus()
        ]);

        $id = $this->connection->lastInsertId();

        if ($id) {
            $reflection = new \ReflectionClass($collect);
            $prop = $reflection->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($collect, (int) $id);
        }

        
    }

    public function findById(int $id): ?Collect
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM collects WHERE id = :id
        ");

        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToEntity($data);
    }

    public function findByCpf(Cpf $cpf): array
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM collects WHERE cpf = :cpf
        ");

        $stmt->execute(['cpf' => (string) $cpf]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToEntity($row), $rows);
    }

    public function update(Collect $collect): void
    {
        $stmt = $this->connection->prepare("
            UPDATE collects
            SET amount = :amount,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $collect->getId(),
            'amount' => $collect->getAmount(),
            'status' => $collect->getStatus()
        ]);
    }

    private function mapToEntity(array $data): Collect
    {
        return new Collect(
            $data['id'],
            new Cpf($data['cpf']),
            (float) $data['amount'],
            new \DateTime($data['data_vencimento']),
            $data['status']
        );
    }
}