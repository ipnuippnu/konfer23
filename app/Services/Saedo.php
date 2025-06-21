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
        return config('saedo.api_url', 'https://edo.local.com');
    }

    /**
     * Get the Saedo API key.
     *
     * @return string
     */
    public static function getApiKey(): string
    {
        return config('saedo.api_key', '');
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