<?php

namespace Application\UseCase\Pay;

use Domain\Pay\Entity\Payment;
use Domain\Pay\Repository\PayRepositoryInterface;
use Domain\Debt\Repository\DebtRepositoryInterface;
use Domain\Debt\ValueObject\Cpf;
use Domain\Tax\Gateway\TaxGatewayInterface;

class RegisterPaymentUseCase
{
    private PayRepositoryInterface $payRepository;
    private DebtRepositoryInterface $debtRepository;
    private TaxGatewayInterface $taxGateway;

    public function __construct(
        PayRepositoryInterface $payRepository,
        DebtRepositoryInterface $debtRepository,
        TaxGatewayInterface $taxGateway
    ) {
        $this->payRepository = $payRepository;
        $this->debtRepository = $debtRepository;
        $this->taxGateway = $taxGateway;
    }

    public function execute(array $input): array
    {
        $cpf = new Cpf($input['cpf']);

        $debt = $this->debtRepository->findByCpf($cpf);

        if (!$debt) {
            throw new \Exception();
        }

        $valorComJuros = $this->taxGateway->calcularJuros($debt);                

        if ($valorComJuros > $debt->getValor()) {
            $debt->applyInterest($valorComJuros);
        }


        $payment = Payment::createFromDebt($debt, $valorComJuros);

        $payment->applyToDebt($debt);

        $this->payRepository->save($payment);

        $this->debtRepository->update($debt);

        return [
            'payment_id' => $payment->getId(),
            'valor_pago' => $payment->getAmount()
        ];
    }
}