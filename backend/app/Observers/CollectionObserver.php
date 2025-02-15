<?php

namespace App\Observers;

use App\Enums\ChangeTypesId;
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
        $action = "Collection <b>{$collection->name}</b> created with order <b>{$collection->order}</b>";
        RecentChange::create([
            'board_id' => $collection->board_id,
            'user_id' => Auth::id(),
            'change_type_id' => ChangeTypesId::COLLECTION,
            'action' => $action,
        ]);
    }

    /**
     * Handle the Collection "updated" event.
     */
    public function updated(Collection $collection)
    {
        $action = "";
        if ($collection->isDirty('order')) {
            $oldOrder = $collection->getOriginal('order');
            $newOrder = $collection->order;
            $action = "Collection <b>{$collection->name}</b> order changed from <b>{$oldOrder}</b> to <b>{$newOrder}</b>";
        }
        if ($collection->isDirty('name')) {
            $oldName = $collection->getOriginal('name');
            $newName = $collection->name;
            $action = "Collection name changed from <b>{$oldName}</b> to <b>{$newName}</b>";
        }

        RecentChange::create([
            'board_id' => $collection->board_id,
            'user_id' => Auth::id(),
            'change_type_id' => ChangeTypesId::COLLECTION,
            'action' => $action,
        ]);
    }

    /**
     * Handle the Collection "deleted" event.
     */
    public function deleted(Collection $collection): void
    {
        RecentChange::create([
            'board_id' => $collection->board_id,
            'user_id' => Auth::id(),
            'change_type_id' => ChangeTypesId::COLLECTION,
            'action' => "Collection '{$collection->name}' deleted",
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
