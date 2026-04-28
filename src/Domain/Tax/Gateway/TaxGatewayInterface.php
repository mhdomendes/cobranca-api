<?php

namespace Domain\Tax\Gateway;

use Domain\Debt\Entity\Debt;

interface TaxGatewayInterface
{
    public function calcularJuros(Debt $debt): float;
}