<?php

namespace App\Tests\Unit\Service;

use App\Entity\Category;
use App\Entity\Todo;
use App\Service\CustomCategoryService;
use App\Service\CustomTodoService;
use App\Tests\Enum\StaticData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomTodoServiceTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var CustomCategoryService
     */
    protected $customCategoryService;

    /**
     * @var CustomTodoService
     */
    protected $customTodoService;

    protected function setUp(): void
    {
        // Container from Kernal
        $kernel = self::bootKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Services
        $this->customCategoryService = $container->get(CustomCategoryService::class);
        $this->customTodoService = $container->get(CustomTodoService::class);
    }

    /**
     * @dataProvider findByConditionsProvider
     */
    public function testFindByConsitions(array $conditions, string $order): void
    {
        $result = $this->customTodoService->findByConditions($conditions, $order);

        $this->assertIsArray($result);
        $this->assertCount(StaticData::getDefaultNumberOfCategoriesAndTodos(), $result);

        for ($i = 0; $i < count($result); $i++) {
            $todo_1 = $result[$i];

            $this->assertInstanceOf(Todo::class, $todo_1);
            
            // Getters
            $title_1 = $todo_1->getTitle();
            $content_1 = $todo_1->getContent();
            $category_1 = $todo_1->getCategory();

            $this->assertIsString($title_1);
            $this->assertIsString($content_1);

            if (isset($result[$i+1])) {
                $title_2 = $result[$i+1]->getTitle();

                if ($order == 'ASC') {
                    $this->assertTrue($title_1 < $title_2);
                } else {
                    $this->assertTrue($title_1 > $title_2);
                }
            }

            // Setters
            $todo_1->setTitle('new title 1');
            $todo_1->setContent('new content 1');
            $todo_1->setCategory($category_1);

            // New getters
            $new_title_1 = $todo_1->getTitle();
            $new_content_1 = $todo_1->getContent();
            $new_category_1 = $todo_1->getCategory();

            // New asserts
            $this->assertNotSame($title_1, $new_title_1);
            $this->assertNotSame($content_1, $new_content_1);
            $this->assertSame($category_1, $new_category_1);
        }
    }

    // Provider
    private function findByConditionsProvider(): array
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
    
    /**
     * @dataProvider findByCategoryProvider
     */
    public function testFindByCategory(array $conditions, bool $good): void
    {
        $categories = $this->customCategoryService->findByConditions($conditions);
        $this->assertIsArray($categories);

        if ($good) {
            foreach($categories as $category) {
                $this->assertInstanceOf(Category::class, $category);
                $idInput = $conditions['id'];
                $idOutput = $category->getId();
                $this->assertSame($idInput, $idOutput);
            }
        } else {
            $this->assertCount(0, $categories);
        }
    }

    // Provider
    private function findByCategoryProvider(): array
    {
        $goodResult = [
            'good_1' => [
                'conditions' => [
                    'id' => rand(1, StaticData::getDefaultNumberOfCategoriesAndTodos() )
                ],
                'good' => true
            ]
        ];

        $badResult = [
            'bad_1' => [
                'conditions' => [
                    'id' => rand(StaticData::getDefaultNumberOfCategoriesAndTodos() + 1, 99)
                ],
                'good' => false
            ]
        ];

        return array_merge($goodResult, $badResult);
    }
}
