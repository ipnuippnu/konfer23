<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Saedo
{
    /**
     * Get the Saedo API URL.
     *
     * @return string
     */
    public static function getApiUrl(): string
    {
        return config('konfer.saedo_url');
    }

    public static function getInstance()
    {
        return Http::withOptions([
            'base_uri' => self::getApiUrl(),
            'verify' => false
        ]);
    }

    public static function get($endpoint, $params = [])
    {
        return self::getInstance()->get($endpoint, $params)->json();
    }
}