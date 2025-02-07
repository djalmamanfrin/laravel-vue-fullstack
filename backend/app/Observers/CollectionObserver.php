<?php

namespace App\Observers;

use App\Models\Collection;
use App\Models\RecentChange;
use Illuminate\Support\Facades\Auth;

class CollectionObserver
{
    /**
     * Handle the Collection "created" event.
     */
    public function created(Collection $collection)
    {
        $loggedInUser = Auth::user();
        $action = "Collection '{$collection->name}' created with order {$collection->order} by " . $loggedInUser->name;
        RecentChange::create([
            'board_id' => $collection->board_id,
            'user_id' => $loggedInUser->id ?? 1,
            'action' => $action,
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Collection "updated" event.
     */
    public function updated(Collection $collection)
    {
        $loggedInUser = Auth::user();
        if ($collection->isDirty('order')) {
            $oldOrder = $collection->getOriginal('order');
            $newOrder = $collection->order;

            $action = "Collection order changed from {$oldOrder} to {$newOrder} by " . $loggedInUser->name;
            RecentChange::create([
                'board_id' => $collection->board_id,
                'user_id' => $loggedInUser->id,
                'action' => $action,
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Collection "deleted" event.
     */
    public function deleted(Collection $collection): void
    {
        $loggedInUser = Auth::user();
        RecentChange::create([
            'board_id' => $collection->board_id,
            'user_id' => $loggedInUser->id ?? 1,
            'action' => "Collection '{$collection->name}' deleted by " . $loggedInUser->name,
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Collection "restored" event.
     */
    public function restored(Collection $collection): void
    {
        //
    }

    /**
     * Handle the Collection "force deleted" event.
     */
    public function forceDeleted(Collection $collection): void
    {
        //
    }
}
