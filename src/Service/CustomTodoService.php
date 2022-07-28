<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Todo;
use App\Repository\TodoRepository;

class CustomTodoService
{ 
    protected $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    /**
     * @param string $order
     * 
     * @return Todo[]
     */
    public function findByConditions(array $conditions = [], string $order = 'ASC'): array
    {
        return $this->todoRepository->findBy(
            $conditions, 
            ['title' => $order]
        );
    }
}
