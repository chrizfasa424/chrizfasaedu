<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trusted proxies are configured through TRUSTED_PROXIES.
     * Set TRUSTED_PROXIES=* only when traffic is guaranteed to pass through a trusted edge.
     */
    protected $proxies;

    /**
     * Use standard forwarded headers to preserve original request scheme/host.
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;

    public function __construct()
    {
        $configured = trim((string) env('TRUSTED_PROXIES', ''));

        if ($configured === '' || strtolower($configured) === 'null') {
            $this->proxies = null;
            return;
        }

        if ($configured === '*') {
            $this->proxies = '*';
            return;
        }

        $this->proxies = array_values(array_filter(array_map('trim', explode(',', $configured))));
    }
}
