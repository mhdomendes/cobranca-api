<?php

namespace Domain\Pay\Entity;

use Domain\Debt\Entity\Debt;

class Payment
{
    private ?int $id;
    private int $debtId;
    private float $amount;
    private \DateTime $paidAt;

    public function __construct(
        ?int $id,
        int $debtId,
        float $amount,
        \DateTime $paidAt
    ) {
        $this->id = $id;
        $this->debtId = $debtId;
        $this->amount = $amount;
        $this->paidAt = $paidAt;

        $this->validate();
    }
    
    public static function createFromDebt(Debt $debt, float $amount): self
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Valor de pagamento inválido');
        }

        return new self(
            null,
            $debt->getId(),
            $amount,
            new \DateTime()
        );
    }

    private function validate(): void
    {
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException('Valor deve ser maior que zero');
        }
    }
    
    public function applyToDebt(Debt $debt): void
    {
        if ($debt->isPaid()) {
            throw new \DomainException('Dívida já está paga');
        }

        if ($this->amount < $debt->getValor()) {
            throw new \DomainException('Pagamento insuficiente');
        }
        
        $debt->markAsPaid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebtId(): int
    {
        return $this->debtId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPaidAt(): \DateTime
    {
        return $this->paidAt;
    }
}