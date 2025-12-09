<?php

namespace App\Listeners;

use App\Services\impl\SearchHistoryService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class MigrateSearchHistory
{
    protected $searchHistoryService;

    /**
     * Create the event listener.
     */
    public function __construct(SearchHistoryService $searchHistoryService)
    {
        $this->searchHistoryService = $searchHistoryService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionId = Session::getId();

        // Migrate search history from session to user
        $this->searchHistoryService->migrateSessionToUser($user->id, $sessionId);
    }
}
