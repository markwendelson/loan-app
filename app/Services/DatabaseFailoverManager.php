<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DatabaseFailoverManager
{
    private const CACHE_KEY = 'db_connection_status';
    private const CACHE_TTL = 60; // 1 minute

    private string $primaryConnection = 'mysql';
    private string $fallbackConnection = 'mysql_local';
    private int $healthCheckTimeout;
    private int $retryAttempts;

    public function __construct()
    {
        $this->healthCheckTimeout = config('database.health_check_timeout', 3);
        $this->retryAttempts = config('database.retry_attempts', 2);
    }

    /**
     * Get the active database connection name
     */
    public function getActiveConnection(): string
    {
        if (!config('database.failover_enabled', true)) {
            return $this->primaryConnection;
        }

        // Check cache first
        $cachedStatus = Cache::get(self::CACHE_KEY);
        if ($cachedStatus !== null) {
            return $cachedStatus;
        }

        // Perform health check
        if ($this->isCloudDatabaseAvailable()) {
            $this->setActiveConnection($this->primaryConnection);
            return $this->primaryConnection;
        }

        Log::warning('Cloud database unavailable, failing over to local database');
        $this->setActiveConnection($this->fallbackConnection);
        return $this->fallbackConnection;
    }

    /**
     * Check if cloud database is available
     */
    public function isCloudDatabaseAvailable(): bool
    {
        for ($attempt = 1; $attempt <= $this->retryAttempts; $attempt++) {
            try {
                DB::connection($this->primaryConnection)
                    ->getPdo()
                    ->query('SELECT 1');
                return true;
            } catch (\Exception $e) {
                if ($attempt < $this->retryAttempts) {
                    usleep(500000); // 0.5 second delay between retries
                }
            }
        }

        return false;
    }

    /**
     * Check if currently using local database
     */
    public function isUsingLocalDatabase(): bool
    {
        return $this->getActiveConnection() === $this->fallbackConnection;
    }

    /**
     * Force reconnection check (clears cache)
     */
    public function forceReconnectionCheck(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Set active connection in cache
     */
    private function setActiveConnection(string $connection): void
    {
        Cache::put(self::CACHE_KEY, $connection, self::CACHE_TTL);
    }
}
