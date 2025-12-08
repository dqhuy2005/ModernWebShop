<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\impl\AuthService;

class CleanupExpiredTokens extends Command
{
    protected $signature = 'tokens:cleanup';

    protected $description = 'Clean up expired refresh tokens from database';

    public function handle(AuthService $authService)
    {
        $this->info('Starting cleanup of expired refresh tokens...');

        $deletedCount = $authService->cleanupExpiredTokens();

        $this->info("Cleanup completed. Deleted {$deletedCount} expired tokens.");

        return Command::SUCCESS;
    }
}
