<?php

namespace Interface\Http\Controller;

use Application\UseCase\Pay\RegisterPaymentUseCase;

class PayController
{
    private RegisterPaymentUseCase $useCase;

    public function __construct(RegisterPaymentUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function pay(): array
    {
        try {            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = $this->useCase->execute($data);
            
            return [
                'success' => true,
                'data' => $result
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}