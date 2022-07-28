<?php
// api/src/DataProvider/Collection/CategoryCollectionDataProvider.php

namespace App\DataProvider\Collection;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Category;
use App\Service\CustomCategoryService;

final class CategoryCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $currentRequest;
    private $customCategoryService;

    public function __construct(RequestStack $requestStack, CustomCategoryService $customCategoryService)
    {
        $this->currentRequest = $requestStack->getCurrentRequest();
        $this->customCategoryService = $customCategoryService;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Category::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $response = [];

        // Incomming collection URI
        $uri = $this->currentRequest->getRequestUri();

        // Custom response (Check repositories, services ...)
        switch ($operationName) {
            case 'get' :
                $response = $this->customCategoryService->findByConditions();                
                break;
        }

        return $response;
    }
}