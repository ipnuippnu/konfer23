<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        '9de388a4f91cebdc9e716828fe37c310ca1a36e9', //SSO_SESSION
        '1d77dedf56f1a487913a81b103ca650bc52f1e87', //SSO_REMEMBER
    ];
}
