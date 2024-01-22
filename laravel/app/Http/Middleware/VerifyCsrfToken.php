<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'qp/cb/frontend',
        'qp/cb/backend',
        'qp/cb/backend2',
        '/callback/scb-billpayment-verify',
        '/callback/scb-billpayment-confirm',
    ];
}
