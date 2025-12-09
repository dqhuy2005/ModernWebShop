<?php

namespace App\Console\Commands;

use App\Services\impl\SearchHistoryService;
use Illuminate\Console\Command;

class CleanOldSearchHistories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-history:clean {--days=30 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old search history records older than specified days';

    protected $searchHistoryService;

    /**
     * Create a new command instance.
     */
    public function __construct(SearchHistoryService $searchHistoryService)
    {
        parent::__construct();
        $this->searchHistoryService = $searchHistoryService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        $this->info("Cleaning search histories older than {$days} days...");

        $deletedCount = $this->searchHistoryService->cleanOldHistories($days);

        if ($deletedCount > 0) {
            $this->info("Successfully deleted {$deletedCount} old search history records.");
        } else {
            $this->info("No old records found to delete.");
        }

        return Command::SUCCESS;
    }
}
