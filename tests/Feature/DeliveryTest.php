<?php

namespace Tests\Feature;

use App\Services\Delivery\FastProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Mockery\Mock;
use Tests\Feature\Mock\FastProviderMock;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testCalculate()
    {
        $data = [
            'sourceKladr' => Str::random(),
            'targetKladr' => Str::random(),
            'weight' => random_int(10,100)/10,
        ];
        $this->get(route('calculate', $data))
            ->dump()
            ->assertSuccessful()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'price',
                    'date',
                    'error'
                ]
            ]);
    }
}
