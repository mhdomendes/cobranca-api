<?php

use Interface\Http\Controller\CollectController;
use Interface\Http\Controller\PayController;

use Application\UseCase\Collect\CreateCollectUseCase;
use Application\UseCase\Pay\RegisterPaymentUseCase;

use Infrastructure\Repository\Collect\CollectRepositoryPostgres;
use Infrastructure\Repository\Pay\PayRepositoryPostgres;
use Infrastructure\Repository\Debt\DebtRepositoryPostgres;

use Infrastructure\Gateway\Tax\TaxPixGateway;

return [
    
    [
        'method' => 'POST',
        'path' => '/collect',
        'action' => function () {

            $useCase = new CreateCollectUseCase(
                new CollectRepositoryPostgres()
            );

            $controller = new CollectController($useCase);

            return $controller->create();
        }
    ],    
    [
        'method' => 'POST',
        'path' => '/pay',
        'action' => function () {

            $useCase = new RegisterPaymentUseCase(
                new PayRepositoryPostgres(),
                new DebtRepositoryPostgres(),
                new TaxPixGateway() 
            );

            $controller = new PayController($useCase);

            return $controller->pay();
        }
    ]
];