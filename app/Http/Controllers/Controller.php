<?php

namespace App\Http\Controllers;

use App\Services\Delivery\FastProvider;
use App\Services\Delivery\Provider;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function calculate()
    {
        $data = request()->validate([
            'sourceKladr' => 'required|string',
            'targetKladr' => 'required|string',
            'weight' => 'required|int',
        ]);
        $response = [];
        foreach (config('services.delivery.providers') as $providerClass) {
            /** @var Provider $provider */
            $provider = resolve($providerClass, $data);
            $provider->calculate();
            $response[$provider->getName()] = $provider->asArray();
        }
        return $response;
    }
}
