<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HomePageService;

class ShowCacheStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show homepage cache statistics';

    /**
     * Execute the console command.
     */
    public function handle(HomePageService $homePageService): int
    {
        $this->info('Homepage Cache Statistics');
        $this->newLine();
        
        $stats = $homePageService->getCacheStats();
        
        $hits = 0;
        $misses = 0;
        
        $headers = ['Cache Key', 'Status', 'TTL (seconds)'];
        $rows = [];
        
        if (isset($stats['keys'])) {
            foreach ($stats['keys'] as $key => $info) {
                $status = $info['status'];
                $ttl = $info['ttl'] > 0 ? $info['ttl'] : 'N/A';
                $rows[] = [$key, $status, $ttl];
                
                if ($status === 'HIT') {
                    $hits++;
                } else {
                    $misses++;
                }
            }
        }
        
        $this->table($headers, $rows);
        
        if (isset($stats['summary'])) {
            $this->newLine();
            $this->info("Total Keys: {$stats['summary']['total_keys']}");
            $this->info("Cached Keys: {$stats['summary']['cached_keys']}");
            $this->info("Cache Coverage: {$stats['summary']['cache_coverage']}%");
        }
        
        $this->newLine();
        $total = $hits + $misses;
        $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;
        
        $this->info("Hits: {$hits}");
        $this->info("Misses: {$misses}");
        $this->info("Hit Rate: {$hitRate}%");
        
        if ($hitRate < 80) {
            $this->warn('⚠ Cache hit rate is low. Consider running: php artisan cache:warmup-homepage');
        } else {
            $this->info('✓ Cache performance is good!');
        }

        // Show Redis server stats if available
        if (isset($stats['redis_server']) && !empty($stats['redis_server'])) {
            $this->newLine();
            $this->info('Redis Server Stats:');
            foreach ($stats['redis_server'] as $key => $value) {
                $this->line("  {$key}: {$value}");
            }
        }
        
        return Command::SUCCESS;
    }
}
