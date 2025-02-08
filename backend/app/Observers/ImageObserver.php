<?php

namespace App\Observers;

use App\Models\Collection;
use App\Models\Image;
use App\Models\RecentChange;
use Illuminate\Support\Facades\Auth;

class ImageObserver
{
    /**
     * Handle the Image "created" event.
     */
    public function created(Image $image): void
    {
        //
    }

    /**
     * Handle the Image "updated" event.
     */
    public function updated(Image $image)
    {
        $loggedInUser = Auth::user();
        if ($image->isDirty('collection_id')) {
            $oldCollection = Collection::find($image->getOriginal('collection_id'));
            $newCollection = Collection::find($image->collection_id);

            $oldCollectionName = $oldCollection->name ?? 'My local images';
            $newCollectionName = $newCollection->name ?? 'My local images';

            $action = match (true) {
                is_null($oldCollection) && !is_null($newCollection) => "Image moved to '{$newCollectionName}' by {$loggedInUser->name}",
                !is_null($oldCollection) && is_null($newCollection) => "Image rolled back from '{$oldCollectionName}' to My local images by {$loggedInUser->name}",
                default => "Image {$image->title} moved from '{$oldCollectionName}' to '{$newCollectionName}' by {$loggedInUser->name}",
            };

            $boardId = $newCollection->board_id ?? $oldCollection->board_id;
            RecentChange::create([
                'board_id' => $boardId,
                'user_id' => $loggedInUser->id,
                'action' => $action,
            ]);
        }
    }

    /**
     * Handle the Image "deleted" event.
     */
    public function deleted(Image $image): void
    {
        //
    }

    /**
     * Handle the Image "restored" event.
     */
    public function restored(Image $image): void
    {
        //
    }

    /**
     * Handle the Image "force deleted" event.
     */
    public function forceDeleted(Image $image): void
    {
        //
    }
}
