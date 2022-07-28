<?php
// api/src/DataProvider/Collection/CategoryCollectionDataProvider.php

namespace App\DataProvider\Item;

use App\Entity\Category;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Service\CustomCategoryService;
use App\Tools\ErrorsMessages;

final class CategoryItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $customCategoryService;

    public function __construct(CustomCategoryService $customCategoryService)
    {
        $this->customCategoryService = $customCategoryService;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Category::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): mixed
    {
        $category = $this->customCategoryService->findByConditions(['id' => $id]);

        return count($category) && $category[0] instanceof Category ? $category[0] : [
            'Erreur' => ErrorsMessages::CATEGORIE_INEXISTANTE
        ];
    }
}