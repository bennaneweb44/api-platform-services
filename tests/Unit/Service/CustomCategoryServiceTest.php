<?php

namespace App\Tests\Unit\Service;

use App\Entity\Category;
use App\Service\CustomCategoryService;
use App\Tests\Enum\StaticData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomCategoryServiceTest extends KernelTestCase
{
    /**
     * @var CustomCategoryService
     */
    protected $customCategoryService;

    protected function setUp(): void
    {
        // Container from Kernal
        $kernel = self::bootKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Custom Category Service
        $this->customCategoryService = $container->get(CustomCategoryService::class);
    }

    /**
     * @dataProvider categoriesProvider
     */
    public function testFindByConsitions($conditions, $order): void
    {
        $result = $this->customCategoryService->findByConditions($conditions, $order);

        $this->assertIsArray($result);
        $this->assertCount(StaticData::getDefaultNumberOfCategoriesAndTodos(), $result);

        for ($i = 0; $i < StaticData::getDefaultNumberOfCategoriesAndTodos(); $i++) {
            $category_1 = $result[$i];            
            $this->assertInstanceOf(Category::class, $category_1);

            // Getters
            $name_1 = $category_1->getName();
            $this->assertIsString($name_1);

            // Next item
            if (isset($result[$i+1])) {
                $category_2 = $result[$i+1];
                $this->assertInstanceOf(Category::class, $category_2);
                $name_2 = $category_2->getName();

                if ($order == 'ASC') {
                    $this->assertTrue($name_1 < $name_2);
                } else {
                    $this->assertTrue($name_1 > $name_2);
                }
            }

            // Setters
            $category_1->setName('new_name_1');
            $new_name_1 = $category_1->getName();

            $this->assertNotSame($name_1, $new_name_1);
        }
    }

    // Provider
    private function categoriesProvider(): array
    {
        $asc = [
            'asc_1' => [
                'conditions' => [],
                'order' => 'ASC'
            ],
        ];

        $desc = [
            'desc_1' => [
                'conditions' => [],
                'order' => 'DESC'
            ],
        ];

        return array_merge($asc, $desc);
    }
}