<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WriteQueueService;
use App\Services\DatabaseFailoverManager;

class ProcessWriteQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:process-writes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process queued write operations when cloud database is available';

    /**
     * Execute the console command.
     */
    public function handle(
        WriteQueueService $queueService,
        DatabaseFailoverManager $failoverManager
    ) {
        // Only process if cloud is available
        if ($failoverManager->isUsingLocalDatabase()) {
            $this->warn('Cloud database still unavailable. Skipping queue processing.');
            return 0;
        }

        $this->info('Processing queued write operations...');

        $results = $queueService->processPendingOperations();

        if ($results['processed'] === 0) {
            $this->info('No pending operations to process.');
            return 0;
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $results['processed']],
                ['Succeeded', $results['succeeded']],
                ['Failed', $results['failed']],
            ]
        );

        if (!empty($results['errors'])) {
            $this->error('Some operations failed:');
            foreach ($results['errors'] as $error) {
                $this->line("  - Queue #{$error['id']}: {$error['error']}");
            }
        }

        $this->info('Queue processing completed!');
        return 0;
    }
}
