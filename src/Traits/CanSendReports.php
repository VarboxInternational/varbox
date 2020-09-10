<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Support\Facades\Http;

trait CanSendReports
{
    /**
     * @var string
     */
    protected static $endpoint = 'https://varbox.io/api/reports/store';

    /**
     * @var string
     */
    protected static $token = 'iNLk4nzIyMt4MJOvnIgLigOEjnkd4PhfzVvLVu9Hfsi4QvekZo';

    /**
     * Check if the application is running in normal conditions.
     *
     * @return bool
     */
    protected function shouldSendReport()
    {
        if ($this->app->isLocal()) {
            return false;
        }

        if ($this->app->runningInConsole()) {
            return false;
        }

        if ($this->app->runningUnitTests()) {
            return false;
        }

        return true;
    }

    /**
     * Send usage statistics to the BackpackForLaravel.com website.
     * Used to track unlicensed usage and general usage statistics.
     *
     * No GDPR implications, since no client info is send, only server info.
     *
     * @return void
     */
    private function sendUsageReport()
    {
        if (!$this->shouldSendReport()) {
            return;
        }

        if (rand(1, 100) != 1) {
            return;
        }

        try {
            Http::withToken(static::$token)->post(static::$endpoint, [
                'app_url' => $_SERVER['HTTP_HOST'] ?? null,
                'laravel_version' => $this->app->version() ?? null,
                'varbox_license' => config('varbox.license.code') ?? null,
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? null,
                'server_name' => $_SERVER['SERVER_NAME'] ?? null,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
            ]);
        } catch (Exception $e) {}
    }
}
