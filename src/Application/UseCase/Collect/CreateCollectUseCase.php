<?php

namespace Application\UseCase\Collect;

use Domain\Collect\Entity\Collect;
use Domain\Collect\Repository\CollectRepositoryInterface;
use Domain\Debt\ValueObject\Cpf;

class CreateCollectUseCase
{
    private CollectRepositoryInterface $collectRepository;

    public function __construct(
        CollectRepositoryInterface $collectRepository
    ) {
        $this->collectRepository = $collectRepository;
    }

    public function execute(array $input): array
    {        
        if (empty($input['amount']) || empty($input['cpf']) || empty($input['data_vencimento'])) {
            throw new \InvalidArgumentException('Dados inválidos');
        }
     
        $cpf = new Cpf($input['cpf']);
        
        $dataVencimento = new \DateTime($input['data_vencimento']);
        
        $collect = new Collect(
            null,
            $cpf,
            $input['amount'],
            $dataVencimento
        );
        
        $this->collectRepository->save($collect);
        
        return [
            'id' => $collect->getId(),
            'amount' => $collect->getAmount(),
            'cpf' => (string) $cpf,
            'data_vencimento' => $dataVencimento->format('Y-m-d')
        ];
    }
}