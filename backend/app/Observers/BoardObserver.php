<?php

namespace App\Observers;

use App\Enums\ChangeTypesId;
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
        $action = "Board <b>{$board->name}</b> created";
        RecentChange::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'change_type_id' => ChangeTypesId::BOARD,
            'action' => $action,
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

            $action = "Board name changed from <b>{$oldName}</b> to <b>{$newName}</b>";
            RecentChange::create([
                'board_id' => $board->id,
                'user_id' => Auth::id(),
                'change_type_id' => ChangeTypesId::BOARD,
                'action' => $action,
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
