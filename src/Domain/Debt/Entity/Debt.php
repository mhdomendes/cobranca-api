<?php

namespace Domain\Debt\Entity;

use Domain\Debt\ValueObject\Cpf;

class Debt
{
    private ?int $id;
    private Cpf $cpf;
    private float $valor;
    private string $status;
    private \DateTime $dataVencimento;

    public function __construct(
        ?int $id,
        Cpf $cpf,
        float $valor,
        string $status,
        \DateTime $dataVencimento
    ) {
        $this->id = $id;
        $this->cpf = $cpf;
        $this->valor = $valor;
        $this->status = $status ?? 'PENDING';
        $this->dataVencimento = $dataVencimento;

        $this->validate();
    }
    
    public static function create(
        Cpf $cpf,
        float $valor,
        \DateTime $dataVencimento
    ): self {
        return new self(null, $cpf, $valor, 'PENDING', $dataVencimento);
    }

    private function validate(): void
    {
        if ($this->valor <= 0) {
            throw new \InvalidArgumentException('Valor da dívida deve ser maior que zero');
        }
    }

    public function applyInterest(float $novoValor): void
    {
        if ($this->isPaid()) {
            throw new \DomainException('Não é possível aplicar juros em dívida paga');
        }

        if ($novoValor <= $this->valor) {
            throw new \DomainException('Valor com juros deve ser maior que o atual');
        }

        $this->valor = $novoValor;
    }

    public function markAsPaid(): void
    {
        if ($this->status === 'PAID') {
            throw new \DomainException('Dívida já está paga');
        }

        $this->status = 'PAID';
    }

    public function isPaid(): bool
    {
        return $this->status === 'PAID';
    }

    public function isOverdue(): bool
    {
        return (new \DateTime()) > $this->dataVencimento && !$this->isPaid();
    }  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpf(): Cpf
    {
        return $this->cpf;
    }

    public function getValor(): float
    {
        return $this->valor;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDataVencimento(): \DateTime
    {
        return $this->dataVencimento;
    }
}