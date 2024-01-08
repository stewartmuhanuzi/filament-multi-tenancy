<?php

namespace App\Observers;

use App\Models\Issue;
use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IssueObserver
{
    public function creating(Issue $issue): void
    {
        Log::info('Creating issue observer called');

        $user = auth::user();
        $tenant = Filament::getTenant(Team::class);

        if ($user) {
            Log::info('Authenticated user found');

            $issue->created_by = $user->id;
            $issue->team_id = $tenant->id;
        }
    }

    /**
     * Handle the Issue "created" event.
     */
    public function created(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "updated" event.
     */
    public function updated(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "deleted" event.
     */
    public function deleted(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "restored" event.
     */
    public function restored(Issue $issue): void
    {
        //
    }

    /**
     * Handle the Issue "force deleted" event.
     */
    public function forceDeleted(Issue $issue): void
    {
        //
    }
}
