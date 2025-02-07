<?php

namespace App\Observers;

use App\Models\Board;
use App\Models\RecentChange;
use Illuminate\Support\Facades\Auth;

class BoardObserver
{
    /**
     * Handle the Board "created" event.
     */
    public function created(Board $board): void
    {
        $loggedInUser = Auth::user();
        $action = "Board '{$board->name}' created by " . $loggedInUser->name;
        RecentChange::create([
            'board_id' => $board->id,
            'user_id' => $loggedInUser->id,
            'action' => $action,
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Board "updated" event.
     */
    public function updated(Board $board): void
    {
        if ($board->isDirty('name')) {
            $oldName = $board->getOriginal('name');
            $newName = $board->name;

            $action = "Board name changed from {$oldName} to {$newName} by " . $loggedInUser->name;
            RecentChange::create([
                'board_id' => $board->board_id,
                'user_id' => $loggedInUser->id,
                'action' => $action,
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Board "deleted" event.
     */
    public function deleted(Board $board): void
    {
        //
    }

    /**
     * Handle the Board "restored" event.
     */
    public function restored(Board $board): void
    {
        //
    }

    /**
     * Handle the Board "force deleted" event.
     */
    public function forceDeleted(Board $board): void
    {
        //
    }
}
