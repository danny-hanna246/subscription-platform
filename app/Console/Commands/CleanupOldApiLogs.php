<?php
// app/Console/Commands/CleanupOldApiLogs.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiAccessLog;

class CleanupOldApiLogs extends Command
{
    protected $signature = 'api:cleanup-logs {--days=30}';
    protected $description = 'Clean up old API access logs';

    public function handle()
    {
        $days = $this->option('days');

        $deleted = ApiAccessLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} old API access logs (older than {$days} days)");

        return Command::SUCCESS;
    }
}
