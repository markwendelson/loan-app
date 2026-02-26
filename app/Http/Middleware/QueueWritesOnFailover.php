<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DatabaseFailoverManager;
use App\Services\WriteQueueService;

class QueueWritesOnFailover
{
    public function __construct(
        private DatabaseFailoverManager $failoverManager,
        private WriteQueueService $queueService
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        // Check if using local database (cloud unavailable)
        if ($this->failoverManager->isUsingLocalDatabase()) {
            // Queue write operations instead of blocking them
            if (
                $request->isMethod('post') ||
                $request->isMethod('put') ||
                $request->isMethod('patch') ||
                $request->isMethod('delete')
            ) {

                // Queue the operation
                $queueId = $this->queueService->queueOperation([
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'route' => $request->route()?->getName(),
                    'data' => $request->all(),
                    'user_id' => auth()->id(),
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'queued',
                        'message' => 'Cloud database unavailable. Your changes have been queued and will sync automatically.',
                        'queue_id' => $queueId,
                        'pending_count' => $this->queueService->getPendingCount()
                    ], 202); // 202 Accepted
                }

                return redirect()->back()->with(
                    'warning',
                    'Cloud database unavailable. Your changes have been queued and will sync automatically.'
                );
            }
        }

        return $next($request);
    }
}
