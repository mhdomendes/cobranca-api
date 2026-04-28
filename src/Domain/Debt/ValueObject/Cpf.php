<?php

namespace Domain\Debt\ValueObject;

class Cpf
{
    private string $value;

    public function __construct(string $cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (!$this->isValid($cpf)) {
            throw new \Exception("CPF inválido");
        }

        $this->value = $cpf;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function isValid(string $cpf): bool
    {
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;

            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }

            $digit = ((10 * $sum) % 11) % 10;

            if ($cpf[$t] != $digit) {
                return false;
            }
        }

        return true;
    }
}