<?php

namespace App\Tests\Functional\EndToEnd;

use App\Entity\Category;
use App\Tests\Enum\StaticData;
use App\Tests\Functional\Common\ApiTesting;

class CategoriesTest extends ApiTesting
{
    /**
     * @method GET
     * @api /api/categories
     * 
     * Retrieves the collection of Category resources
     */
    public function testGetAllCategories(): void
    {
        $this->testGetCollection(StaticData::CATEGORIES_ENDPOINTS_PREFIX, Category::class);
    }

    /**
     * @method GET
     * @api /api/categories/{id}
     * 
     * Retrieves a Category resource
     */
    public function testGetCategory(): void
    {
        $this->testGetItem(StaticData::CATEGORIES_ENDPOINTS_PREFIX, Category::class, 'Category');
    }

    /**
     * @method POST
     * @api /api/categories
     * 
     * Create a category resource
     */
    public function testCreateCategory(): void
    {
        // data
        $newCategory = [
            'name' => 'newCategory'
        ];

        // Request
        $response = $this->client->request('POST', StaticData::CATEGORIES_ENDPOINTS_PREFIX, ['json' => $newCategory]);

        // Response
        $this->assertApiPlatformResponse(201);

        // Json
        $newId = $this->getCurrentNbCategories();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Category',
            '@type' => 'Category',
            '@id' => StaticData::CATEGORIES_ENDPOINTS_PREFIX . '/' . $newId,
            'id' => $newId
        ]);

        // Content
        $content = json_decode($response->getContent());
        $this->assertIsObject($content);

        // Schema
        $this->assertMatchesResourceItemJsonSchema(Category::class);
    }

    /**
     * @method PUT
     * @api /api/categories/{id}
     * 
     * Replace the category resource
     */
    public function testReplaceCategory(): void
    {
        // Last category in DB
        $lastId = $this->getCurrentNbCategories();
        $iri = $this->findIriBy(Category::class, [
            'id' => $lastId
        ]);

        // New Data
        $newData = [
            'name' => 'replacedName'
        ];

        // Request
        $this->client->request('PUT', $iri, ['json' => $newData]);

        // Response
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'replacedName'
        ]);

        // Replaced element
        $replacedCategory = $this->categoryRepository->findOneBy([
            'id' => $lastId
        ]);
        $this->assertNotNull($replacedCategory);
        $this->assertMatchesResourceItemJsonSchema(Category::class);
    }

    /**
     * @method DELETE
     * @api /api/categories/{id}
     * 
     * Removes the category resource
     */
    public function testDeleteCategory(): void
    {
        // Last category in DB
        $lastId = $this->getCurrentNbCategories();
        $iri = $this->findIriBy(Category::class, [
            'id' => $lastId
        ]);

        // Request
        $this->client->request('DELETE', $iri);

        // Response
        $this->assertResponseStatusCodeSame(204);
        $deletedCategory = $this->categoryRepository->findOneBy([
            'id' => $lastId
        ]);
        $this->assertNull($deletedCategory);
    }
}