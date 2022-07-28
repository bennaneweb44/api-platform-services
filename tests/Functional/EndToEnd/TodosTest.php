<?php

namespace App\Tests\Functional\EndToEnd;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response;
use App\Entity\Todo;
use App\Tests\Enum\StaticData;
use App\Tests\Functional\Common\ApiTesting;
use App\Tools\ErrorsMessages;

class TodosTest extends ApiTesting
{
    /**
     * @method GET
     * @api /api/todos
     * 
     * Retrieves the collection of Todo resources
     */
    public function testGetAllTodos(): void
    {
        $this->testGetCollection(StaticData::TODOS_ENDPOINTS_PREFIX, Todo::class);
    }

    /**
     * @method GET
     * @api /api/todos/{id}
     * 
     * Retrieves a Todo resource
     */
    public function testGetTodo(): void
    {
        $this->testGetItem(StaticData::TODOS_ENDPOINTS_PREFIX, Todo::class, 'Todo');
    }

    /**
     * @method GET
     * @api /api/todos/category/{categoryId}
     * 
     * @dataProvider getTodosByCategoryProvider
     */
    public function testGetTodosByCategory(bool $good): void
    {
        // CategoryId
        $categoryId = $good ? rand(1, $this->getCurrentNbCategories()) : rand($this->getCurrentNbCategories() + 1, 99);

        // Request
        $response = static::createClient()->request('GET', StaticData::TODOS_ENDPOINTS_PREFIX . '/category/' . $categoryId);
        $this->assertInstanceOf(Response::class, $response);
        
        // Response
        $this->assertApiPlatformResponse(200);

        // Content
        $content = json_decode($response->getContent());
        $this->assertIsObject($content);

        // Type + nb items
        $this->assertObjectHasAttribute('@type', $content);
        $this->assertJsonContains([
            '@type' => 'hydra:Collection'
        ]);
        $this->assertObjectHasAttribute('hydra:totalItems', $content);
        $this->assertJsonContains([
            'hydra:totalItems' => 1
        ]);

        // Data
        if ($good) {
            $this->assertMatchesResourceCollectionJsonSchema(Todo::class);
        } else {            
            $this->assertJsonContains([
                'hydra:member' => [
                    0 => ErrorsMessages::CATEGORIE_INEXISTANTE
                ]
            ]);
        }
    }
    
    // Provider
    private function getTodosByCategoryProvider(): array
    {
        $goodResult = [
            'good_1' => [
                'good' => true
            ]
        ];

        $badResult = [
            'bad_1' => [
                'good' => false
            ]
        ];

        return array_merge($goodResult, $badResult);
    }

    /**
     * @method POST
     * @api /api/todos
     * 
     * Create a todo resource
     */
    public function testCreateTodo(): void
    {
        // data
        $category = $this->categoryRepository->findOneBy(['id' => $this->getCurrentNbCategories()]);        

        $newTodo = [
            'title' => 'newTitle',
            'content' => 'newContent',
            'category' => StaticData::CATEGORIES_ENDPOINTS_PREFIX . '/' . $category->getId()
        ];

        // Request
        $response = $this->client->request('POST', StaticData::TODOS_ENDPOINTS_PREFIX, ['json' => $newTodo]);
        
        // Response
        $this->assertApiPlatformResponse(201);

        // Json
        $newId = $this->getCurrentNbTodos();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Todo',
            '@type' => 'Todo',
            '@id' => StaticData::TODOS_ENDPOINTS_PREFIX . '/' . $newId,
            'id' => $newId
        ]);

        // Content
        $content = json_decode($response->getContent());
        $this->assertIsObject($content);

        // Schema
        $this->assertMatchesResourceItemJsonSchema(Todo::class);
    }

    /**
     * @method PUT
     * @api /api/todos/{id}
     * 
     * Replace the todo resource
     */
    public function testReplaceTodo(): void
    {
        // Last todo in DB
        $lastId = $this->getCurrentNbTodos();
        $iri = $this->findIriBy(Todo::class, [
            'id' => $lastId
        ]);

        // New Data
        $newData = [
            'title' => 'replacedTitle'
        ];

        // Request
        $this->client->request('PUT', $iri, ['json' => $newData]);

        // Response
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'replacedTitle'
        ]);

        // Replaced element
        $replacedTodo = $this->todoRepository->findOneBy([
            'id' => $lastId
        ]);
        $this->assertNotNull($replacedTodo);
        $this->assertMatchesResourceItemJsonSchema(Todo::class);
    }

    /**
     * @method DELETE
     * @api /api/todos/{id}
     * 
     * Removes the todo resource
     */
    public function testDeleteTodo(): void
    {
        // Last todo in DB
        $lastId = $this->getCurrentNbTodos();
        $iri = $this->findIriBy(Todo::class, [
            'id' => $lastId
        ]);

        // Request
        $this->client->request('DELETE', $iri);

        // Response
        $this->assertResponseStatusCodeSame(204);
        $deletedTodo = $this->todoRepository->findOneBy([
            'id' => $lastId
        ]);
        $this->assertNull($deletedTodo);
    }
}