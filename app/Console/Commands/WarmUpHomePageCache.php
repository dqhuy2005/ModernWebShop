<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\impl\HomePageService;

class WarmUpHomePageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warmup-homepage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up homepage cache by pre-loading all data';

    /**
     * Execute the console command.
     */
    public function handle(HomePageService $homePageService): int
    {
        $this->info('Starting homepage cache warm-up...');

        $startTime = microtime(true);

        $homePageService->warmUpCache();

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->info("✓ Cache warm-up completed in {$duration}ms");

        // Show cache stats
        $this->newLine();
        $this->info('Cache Status:');
        $stats = $homePageService->getCacheStats();

        if (isset($stats['keys'])) {
            foreach ($stats['keys'] as $key => $info) {
                $status = $info['status'];
                $ttl = $info['ttl'];
                $icon = $status === 'HIT' ? '✓' : '✗';
                $ttlText = $ttl > 0 ? " (TTL: {$ttl}s)" : '';
                $this->line("  {$icon} {$key}: {$status}{$ttlText}");
            }
        }

        if (isset($stats['summary'])) {
            $this->newLine();
            $this->info('Summary:');
            $this->line("  Total keys: {$stats['summary']['total_keys']}");
            $this->line("  Cached keys: {$stats['summary']['cached_keys']}");
            $this->line("  Cache coverage: {$stats['summary']['cache_coverage']}%");
        }

        return Command::SUCCESS;
    }
}
