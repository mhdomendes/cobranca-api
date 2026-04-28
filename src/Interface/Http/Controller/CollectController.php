<?php

namespace Interface\Http\Controller;

use Application\UseCase\Collect\CreateCollectUseCase;

class CollectController
{
    private CreateCollectUseCase $useCase;

    public function __construct(CreateCollectUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function create(): array
    {        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $result = $this->useCase->execute($data);
        
        return [
            'success' => true,
            'data' => $result
        ];
    }
}