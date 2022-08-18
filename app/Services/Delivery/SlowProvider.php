<?php

namespace App\Services\Delivery;

use Illuminate\Support\Carbon;

class SlowProvider extends Provider
{
    protected const BASE_URL = 'fast.delivery.com';
    protected const BASE_PRICE = 150;
    protected float $coefficient;

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Slow Provider';
    }

    public function parseServiceResponse(): void
    {
        if ($this->hasError())
            return;

        $data = json_decode($this->serviceResponse, true);
        foreach (['coefficient', 'date', 'error'] as $key) {
            if (!key_exists($key, $data)) {
                $this->error = "Service doesn't return $key";
                return;
            }
            $this->{"set".ucfirst($key)}($data[$key]);
        }
    }

    /**
     * @param Carbon|string $date
     * @return void
     */
    public function setDate(Carbon|string $date): void
    {
        $this->date = new Carbon($date);
    }

    /**
     * @param float $coefficient
     */
    public function setCoefficient(float $coefficient): void
    {
        $this->coefficient = $coefficient;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return self::BASE_PRICE * $this->coefficient;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    protected function getFakeResponseBody(): string
    {
        return json_encode([
            'coefficient'=>random_int(10,1000)/100,
            'date'=>now()->addDays(random_int(1,10)),
            'error'=>''
        ]);
    }
}
