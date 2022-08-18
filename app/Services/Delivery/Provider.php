<?php

namespace App\Services\Delivery;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Carbon;

abstract class Provider
{
    protected const BASE_URL = '';

    public float $price;
    public Carbon $date;
    public string $error = '';

    protected string $serviceResponse;

    public function __construct (
        public string $sourceKladr,//кладр откуда везем
        public string $targetKladr,//кладр куда везем
        public float $weight,//вес отправления в кг
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Provider';
    }

    protected function getRequestsParams(): array
    {
        return [
            'sourceKladr' => $this->sourceKladr,
            'targetKladr' => $this->targetKladr,
            'weight' => $this->weight,
        ];
    }

    protected function requestData(): void
    {
//        $client = new Client();
        $client = $this->getFakeClient();
        $params = [
            'query' => $this->getRequestsParams()
        ];
        try {
            $response = $client->get(self::BASE_URL, $params);
        } catch (GuzzleException $exception) {
            $this->error = 'HTTP Error';
            return;
        }
        $this->serviceResponse = $response->getBody()->getContents();
    }

    protected function getFakeClient(): Client
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getFakeResponseBody()),
        ]);

        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }

    protected function getFakeResponseBody(): string
    {
        return json_encode([]);
    }

    protected function parseServiceResponse(): void
    {

    }

    public function hasError(): bool
    {
        return $this->error !== '';
    }

    public function calculate(): void
    {
        $this->requestData();
        $this->parseServiceResponse();
    }

    public function asArray(): array
    {
        if ($this->hasError())
            return [
                'price' => 0.0,
                'date' => '',
                'error' => $this->error,
            ];
        return [
            'price' => $this->getPrice(),
            'date' => $this->getDate()->format('Y-m-d'),
            'error' => $this->error,
        ];
    }

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param Carbon $date
     */
    public function setDate(Carbon $date): void
    {
        $this->date = $date;
    }
}
