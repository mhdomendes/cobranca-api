<?php

namespace Domain\Collect\Entity;

use Domain\Debt\ValueObject\Cpf;

class Collect
{
    private ?int $id;
    private Cpf $cpf;
    private float $amount;
    private \DateTime $dataVencimento;
    private string $status;

    public function __construct(
        ?int $id,
        Cpf $cpf,
        float $amount,
        \DateTime $dataVencimento,
        string $status = 'PENDING'
    ) {
        $this->id = $id;
        $this->cpf = $cpf;
        $this->amount = $amount;
        $this->dataVencimento = $dataVencimento;
        $this->status = $status;

        $this->validate();
    }


    public static function create(
        Cpf $cpf,
        float $amount,
        \DateTime $dataVencimento
    ): self {
        return new self(null, $cpf, $amount, $dataVencimento);
    }

    private function validate(): void
    {
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException('Valor deve ser maior que zero');
        }

        if ($this->dataVencimento < new \DateTime()) {
            throw new \InvalidArgumentException('Data de vencimento inválida');
        }
    }

    public function markAsPaid(): void
    {
        if ($this->status === 'PAID') {
            throw new \DomainException('Cobrança já está paga');
        }

        $this->status = 'PAID';
    }

    public function isOverdue(): bool
    {
        return (new \DateTime()) > $this->dataVencimento && $this->status !== 'PAID';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpf(): Cpf
    {
        return $this->cpf;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDataVencimento(): \DateTime
    {
        return $this->dataVencimento;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
