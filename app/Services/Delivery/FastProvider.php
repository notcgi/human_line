<?php

namespace App\Services\Delivery;

use Illuminate\Support\Carbon;

class FastProvider extends Provider
{
    protected const BASE_URL = 'fast.delivery.com';
    protected const LAST_HOUR = 18;
    protected int $period;

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Fast Provider';
    }

    public function parseServiceResponse(): void
    {
        if ($this->hasError())
            return;

        $data = json_decode($this->serviceResponse, true);
        foreach (['price', 'period', 'error'] as $key) {
            if (!key_exists($key, $data)) {
                $this->error = "Service doesn't return $key";
                return;
            }
            $this->$key = $data[$key];
        }
    }

    public function getDate(): Carbon
    {
        $date = now();
        if ($date->hour >= self::LAST_HOUR)
            $this->period++;
        $date->addDays($this->period);
        $this->date = $date;
        return $this->date;
    }


    protected function getFakeResponseBody(): string
    {
        return json_encode([
            'price'=>random_int(100,10000)/100,
            'period'=>random_int(1,100),
            'error'=>''
        ]);
    }
}
