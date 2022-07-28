<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CustomCategoryService
{ 
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;        
    }

    /**
     * @param string $order
     * 
     * @return Category[]
     */
    public function findByConditions(array $conditions = [], string $order = 'ASC'): array
    {
        return $this->categoryRepository->findBy(
            $conditions, 
            ['name' => $order]
        );
    }
}
