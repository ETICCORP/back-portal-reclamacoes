<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

abstract class AbstractTest extends TestCase
{

    protected $routeName;
    protected $table;
    protected $keyIgnore = [];
    protected $model;
    protected $models = [];

    protected function createItem()
    {
        return $this->model::factory()->create();
    }

    protected function createRelatedItem()
    {
        $mainModel = array_shift($this->models);
        $mainItem = $mainModel::factory()->create();

        foreach ($this->models as $relation => $relatedModel) {

            if (is_int($relation)) {
                $mainItem->{$relatedModel}()->saveMany($relatedModel::factory()->count(3)->make());
                continue;
            }

            $mainItem->{$relation}()->save($relatedModel::factory()->make());

        }

        return $mainItem;
    }

    protected function createData()
    {
        return $this->model::factory()->make()->toArrayData();
    }

    protected function createRelatedData()
    {
        $payloadAssociative = [];
        $payloadNumeric = [];

        foreach ($this->models as $key => $model) {

            if(!is_int($key)) {
                $payloadAssociative[$key] = $model::factory()->make()->toArrayData();
                continue;
            }

            $payloadNumeric[$key] = $model::factory()->make()->toArrayData();
        }

        return array_merge($payloadAssociative, collect($payloadNumeric)->flatMap(function ($item) {
            return is_array($item) ? $item : [$item];
        })->all());

    }

    public function testIndex()
    {
        $url = route($this->routeName);
        $url_paginate = $url . '?paginate=10';

        $response = $this->get($url);
        $response_paginate = $this->get($url_paginate);

        $response->assertStatus(200);
        $response_paginate->assertStatus(200);

        $responseData = $response->json();
        $responseData_paginate = $response_paginate->json();

        // Verifica se a resposta sem paginação é um array
        if (isset($responseData)) {
            $this->assertIsArray($responseData);
        }

        // Verifica se a resposta paginada contém as chaves esperadas
        if (isset($responseData_paginate['data'])) {
            $this->assertArrayHasKey('current_page', $responseData_paginate);
            $this->assertArrayHasKey('data', $responseData_paginate);
        }
    }

    public function testById()
    {
        $item = $this->createItem();

        $primaryKey = $item->getKeyName();
        $id = $item->$primaryKey;

        $url = route($this->routeName) . '/' . $id;

        $response = $this->get($url);

        $response->assertStatus(200);
        $responseData = $response->json();

        if (isset($responseData)) {
            $this->assertIsArray($responseData);
        }
    }

    public function testStore()
    {
        $data = $this->createData();
        $response = $this->post(route($this->routeName), $data);

        if ($response->statusText() == 'Method Not Allowed') {
            $this->markTestSkipped('Rota de criação não implementada');
        }

        $this->assertResponseAndDatabase($response, $data);
    }

    public function testUpdate()
    {
        $item = $this->createItem();
        $primaryKey = $item->getKeyName();
        $id = $item->$primaryKey;

        $updatedData = $this->createData();

        $response = $this->put(route($this->routeName) . '/' . $id, $updatedData);

        if ($response->statusText() == 'Method Not Allowed') {
            $this->markTestSkipped('Rota de atualização não implementada');
        }

        $this->assertResponseAndDatabase($response, $updatedData, 200);
    }

    protected function assertResponseAndDatabase($response, $data, $expectedStatus = 201)
    {
        $response->assertStatus($expectedStatus);
        $data = $this->processData($data);

        $this->assertDatabaseHas($this->table, $data);
    }


    protected function processData(array $data): array
    {
        return array_diff_key($data, array_flip($this->keyIgnore));
    }

    public function testDestroy()
    {
        $item = $this->createItem();
        $primaryKey = $item->getKeyName();
        $id = $item->$primaryKey;

        $url = route($this->routeName) . '/' . $id;

        // Envia a requisição para deletar o item
        $response = $this->delete($url);

        if ($response->statusText() == 'Method Not Allowed') {
            $this->markTestSkipped('Rota de exclusão não implementada');
        }

        $response->assertStatus(204);

        $exists = DB::table($this->table)->where($primaryKey, $id)->exists();

        if ($exists) {
            $this->assertSoftDeleted($this->table, [$primaryKey => $id]);
        } else {
            $this->assertDatabaseMissing($this->table, [$primaryKey => $id]);
        }
    }
}
