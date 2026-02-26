<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DatabaseFailoverManager;
use App\Services\WriteQueueService;

class HealthCheckController extends Controller
{
    public function __invoke(
        DatabaseFailoverManager $failoverManager,
        WriteQueueService $queueService
    ) {
        return response()->json([
            'cloud_available' => !$failoverManager->isUsingLocalDatabase(),
            'active_connection' => $failoverManager->getActiveConnection(),
            'pending_operations' => $queueService->getPendingCount(),
        ]);
    }
}
