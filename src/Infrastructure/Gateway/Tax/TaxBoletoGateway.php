<?php

namespace Infrastructure\Gateway\Tax;

use Domain\Tax\Gateway\TaxGatewayInterface;
use Domain\Debt\Entity\Debt;

class TaxBoletoGateway implements TaxGatewayInterface
{
    public function calcularJuros(Debt $debt): float
    {
        $hoje = new \DateTime();
        $vencimento = $debt->getDataVencimento();

        if ($hoje <= $vencimento) {
            return $debt->getValor();
        }

        $diasAtraso = $vencimento->diff($hoje)->days;

        $juros = 0.2;
        $multa = 0.01 * $diasAtraso;

        return $debt->getValor() * (1 + $juros + $multa);
    }
}