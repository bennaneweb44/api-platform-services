<?php
// api/src/DataProvider/Collection/TodoCollectionDataProvider.php

namespace App\DataProvider\Item;

use App\Entity\Todo;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Service\CustomTodoService;
use App\Tools\ErrorsMessages;

final class TodoItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $customTodoService;

    public function __construct(CustomTodoService $customTodoService)
    {
        $this->customTodoService = $customTodoService;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Todo::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): mixed
    {
        $todo = $this->customTodoService->findByConditions(['id' => $id]);

        return count($todo) && $todo[0] instanceof Todo ? $todo[0] : [
            'Erreur' => ErrorsMessages::TODO_INEXISTANT
        ];
    }
}