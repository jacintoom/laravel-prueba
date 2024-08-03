<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ApiService;

use App\Models\Entity;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $apiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiService = $this->createPartialMock(ApiService::class, ['findStoreEntities']);
    }

    public function testFindStoreEntitiesSuccess()
    {
        $mockResponse = json_encode([
            'entries' => [
                ['API' => 'Test API', 'Description' => 'Test Description', 'Link' => 'http://test.com'],
            ]
        ]);

        $this->apiService->expects($this->once())
            ->method('findStoreEntities')
            ->with('TestCategory')
            ->willReturn($mockResponse);

        $result = $this->apiService->findStoreEntities('TestCategory', 1);

        $this->assertTrue($result);

        $this->assertDatabaseHas('entities', [
            'api' => 'Test API',
            'description' => 'Test Description',
            'link' => 'http://test.com',
            'category_id' => 1
        ]);
    }

    public function testStoreEntities()
    {
        $entities = [
            ['API' => 'Test API 1', 'Description' => 'Test Description 1', 'Link' => 'http://test1.com'],
            ['API' => 'Test API 2', 'Description' => 'Test Description 2', 'Link' => 'http://test2.com'],
        ];

        $method = new \ReflectionMethod(ApiService::class, 'storeEntities');
        $method->setAccessible(true);
        $method->invokeArgs($this->apiService, [$entities, 1]);

        $this->assertDatabaseHas('entities', [
            'api' => 'Test API 1',
            'description' => 'Test Description 1',
            'link' => 'http://test1.com',
            'category_id' => 1
        ]);

        $this->assertDatabaseHas('entities', [
            'api' => 'Test API 2',
            'description' => 'Test Description 2',
            'link' => 'http://test2.com',
            'category_id' => 1
        ]);
    }
}