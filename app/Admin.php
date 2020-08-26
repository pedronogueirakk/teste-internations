<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class Admin extends Model
{
    public function getData() {
        $returnValue = '';

        try {
            $response = Http::retry(3, 100)->get(env('HOST_URL').'data');
            $statusCode = $response->json()['status_code'];

            if ($response->successful() && $statusCode === 500) {
                Cache::put('fullData', $response->body());
                $returnValue = $response->body();
            }

        } catch (Throwable $e) {
            $returnValue = Cache::get('fullData');
        }

        return $returnValue;
    }
}
