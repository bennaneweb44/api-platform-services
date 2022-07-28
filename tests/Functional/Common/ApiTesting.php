<?php

namespace App\Tests\Functional\Common;

use App\Entity\Category;
use App\Entity\Todo;
use App\Repository\CategoryRepository;
use App\Repository\TodoRepository;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ApiTesting extends ApiTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var TodoRepository
     */
    protected $todoRepository;

    protected function setUp(): void
    {
        // Client
        $this->client = static::createClient();

        // Repositories
        $this->categoryRepository = static::getContainer()->get('doctrine')->getRepository(Category::class);
        $this->todoRepository = static::getContainer()->get('doctrine')->getRepository(Todo::class);
    }

    protected function getCurrentNbCategories(): int
    {
        $categories = $this->categoryRepository->findAll();
        return count($categories);
    }

    protected function getCurrentNbTodos(): int
    {
        $todos = $this->todoRepository->findAll();
        return count($todos);
    }    
      
    protected function assertApiPlatformResponse(int $code): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame($code);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /******************************************************************
     ************************ COMMON TESTS **************************** 
     ******************************************************************/  

    /**
     * @method GET
     * @api /api/resources
     * 
     * Retrieves the collection of Resource
     */
    protected function testGetCollection(string $endpoint, string $className): void
    {
        // Request
        $response = $this->client->request('GET', $endpoint);
        $this->assertInstanceOf(Response::class, $response);
        
        // Response
        $this->assertApiPlatformResponse(200);

        // Content
        $content = json_decode($response->getContent());
        $this->assertIsObject($content);

        // @type + total items
        $this->assertObjectHasAttribute('@type', $content);
        $this->assertJsonContains([
            '@type' => 'hydra:Collection'
        ]);
        $this->assertObjectHasAttribute('hydra:totalItems', $content);
        $this->assertJsonContains([
            'hydra:totalItems' => $this->getCurrentNbCategories()
        ]);

        // Data collection
        $this->assertObjectHasAttribute('hydra:member', $content);
        $this->assertMatchesResourceCollectionJsonSchema($className);
    }

    /**
     * @method GET
     * @api /api/resources/{id}
     * 
     * Retrieves a resource
     */
    protected function testGetItem(string $endpointPrefix, string $className, string $resource): void
    {
        // Id
        $id = rand(1, $this->getCurrentNbCategories());

        // Request
        $response = $this->client->request('GET', $endpointPrefix . '/' . $id);
        $this->assertInstanceOf(Response::class, $response);

        // Response
        $this->assertApiPlatformResponse(200);

        // Content
        $content = json_decode($response->getContent());
        $this->assertIsObject($content);

        // @type + @id
        $this->assertObjectHasAttribute('@type', $content);
        $this->assertJsonContains([
            '@type' => $resource
        ]);
        $this->assertJsonContains([
            '@id' => $endpointPrefix . '/' . $id
        ]);

        // Data item
        $this->assertMatchesResourceItemJsonSchema($className);
    }
}