<?php

namespace App\Listeners;

use App\Services\impl\SearchHistoryService;
use Illuminate\Auth\Events\Logout;

class ClearSearchHistoryCache
{
    protected $searchHistoryService;

    /**
     * Create the event listener.
     */
    public function __construct(SearchHistoryService $searchHistoryService)
    {
        $this->searchHistoryService = $searchHistoryService;
    }

    public function handle(Logout $event): void
    {
        $user = $event->user;

        if ($user && method_exists($user, 'getAuthIdentifier')) {
            $this->searchHistoryService->clearUserCache($user->getAuthIdentifier());
        }
    }
}
