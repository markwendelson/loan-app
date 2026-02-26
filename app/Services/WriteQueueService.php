<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WriteQueueService
{
    /**
     * Queue a write operation
     */
    public function queueOperation(array $data): int
    {
        $queueId = DB::connection('mysql_local')
            ->table('write_queue')
            ->insertGetId([
                'method' => $data['method'],
                'url' => $data['url'],
                'route' => $data['route'] ?? null,
                'payload' => json_encode($data['data']),
                'user_id' => $data['user_id'],
                'status' => 'pending',
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        Log::info("Queued operation #{$queueId}: {$data['method']} {$data['url']}");

        return $queueId;
    }

    /**
     * Process all pending operations
     */
    public function processPendingOperations(): array
    {
        $operations = DB::connection('mysql_local')
            ->table('write_queue')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $results = [
            'processed' => 0,
            'succeeded' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($operations as $operation) {
            $results['processed']++;

            try {
                $this->executeOperation($operation);
                $this->markAsCompleted($operation->id);
                $results['succeeded']++;
            } catch (\Exception $e) {
                $this->markAsFailed($operation->id, $e->getMessage());
                $results['failed']++;
                $results['errors'][] = [
                    'id' => $operation->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Execute a queued operation
     */
    private function executeOperation($operation): void
    {
        // Temporarily switch to cloud database
        $originalConnection = config('database.default');
        config(['database.default' => 'mysql']);

        try {
            $payload = json_decode($operation->payload, true);

            // Re-execute the operation based on route
            switch ($operation->route) {
                case 'loans.store':
                    $controller = app(\App\Http\Controllers\LoanController::class);
                    $request = \Illuminate\Http\Request::create($operation->url, $operation->method, $payload);
                    $controller->store($request);
                    break;

                case 'loans.update':
                    $controller = app(\App\Http\Controllers\LoanController::class);
                    $request = \Illuminate\Http\Request::create($operation->url, $operation->method, $payload);
                    $controller->update($request, $payload['id'] ?? null);
                    break;

                case 'clients.store':
                    $controller = app(\App\Http\Controllers\ClientController::class);
                    $request = \Illuminate\Http\Request::create($operation->url, $operation->method, $payload);
                    $controller->store($request);
                    break;

                case 'clients.update':
                    $controller = app(\App\Http\Controllers\ClientController::class);
                    $request = \Illuminate\Http\Request::create($operation->url, $operation->method, $payload);
                    $controller->update($request, $payload['id'] ?? null);
                    break;

                // Add more routes as needed

                default:
                    Log::warning("Unknown route for queued operation: {$operation->route}");
                    throw new \Exception("Route {$operation->route} not configured for queue processing");
            }
        } finally {
            // Always restore original connection
            config(['database.default' => $originalConnection]);
        }
    }

    /**
     * Mark operation as completed
     */
    private function markAsCompleted(int $id): void
    {
        DB::connection('mysql_local')
            ->table('write_queue')
            ->where('id', $id)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Mark operation as failed
     */
    private function markAsFailed(int $id, string $error): void
    {
        DB::connection('mysql_local')
            ->table('write_queue')
            ->where('id', $id)
            ->update([
                'status' => 'failed',
                'error_message' => $error,
                'attempts' => DB::raw('attempts + 1'),
                'updated_at' => now(),
            ]);
    }

    /**
     * Get count of pending operations
     */
    public function getPendingCount(): int
    {
        try {
            return DB::connection('mysql_local')
                ->table('write_queue')
                ->where('status', 'pending')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Retry failed operations
     */
    public function retryFailedOperations(int $maxAttempts = 3): array
    {
        $operations = DB::connection('mysql_local')
            ->table('write_queue')
            ->where('status', 'failed')
            ->where('attempts', '<', $maxAttempts)
            ->get();

        foreach ($operations as $operation) {
            // Reset to pending for retry
            DB::connection('mysql_local')
                ->table('write_queue')
                ->where('id', $operation->id)
                ->update(['status' => 'pending']);
        }

        return $this->processPendingOperations();
    }
}
