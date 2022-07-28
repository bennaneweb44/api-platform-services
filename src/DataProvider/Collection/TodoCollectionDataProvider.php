<?php
// api/src/DataProvider/Collection/TodoCollectionDataProvider.php

namespace App\DataProvider\Collection;

use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Category;
use App\Entity\Todo;
use App\Service\CustomCategoryService;
use App\Service\CustomTodoService;
use App\Tools\ErrorsMessages;

final class TodoCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $currentRequest;
    
    private $customCategoryService;
    private $customTodoService;

    public function __construct(
        RequestStack $requestStack,
        CustomCategoryService $customCategoryService,
        CustomTodoService $customTodoService
    ) {
        $this->currentRequest = $requestStack->getCurrentRequest();
        $this->customCategoryService = $customCategoryService;
        $this->customTodoService = $customTodoService;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Todo::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $response = [];

        // Incomming attributes from request
        $attributes = $this->currentRequest->attributes;

        // Custom response (Check repositories, services ...)
        switch ($operationName) {

            case 'get' :

                $response = $this->customTodoService->findByConditions();                
                break;

            case 'all_with_category' :

                // Get category
                $parameters = $attributes->all();
                $categoryId = isset($parameters['categoryId']) ? $parameters['categoryId'] : 0;
                $category = $this->customCategoryService->findByConditions(['id' => $categoryId]);

                // Response
                $response = count($category) === 1 && $category[0] instanceof Category ?
                            $this->customTodoService->findByConditions(['category' => $category[0]]) :
                            [ErrorsMessages::CATEGORIE_INEXISTANTE];

                break;
        }

        return $response;
    }
}