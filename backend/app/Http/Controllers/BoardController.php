<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    public function index(): JsonResponse
    {
        $boards = Board::where('owner_id', Auth::id())
            ->get()
            ->map(function ($board) {
                $allImages = collect();

                foreach ($board->collections()->get() as $collection) {
                    $allImages = $allImages->merge($collection->images);
                }

                $board->images = $allImages->shuffle()->take(3)->values();
                $board->images_counter = $allImages->count();
                return $board;
            });
        return response()->json($boards);
    }

    public function get(int $boardId): JsonResponse
    {
        $board = Board::with([
            'collections' => function ($query) {
                $query->orderBy('order');
            },
            'collections.images',
            'owner'
        ])->findOrFail($boardId);

        $allImages = collect();
        foreach ($board->collections as $collection) {
            $allImages = $allImages->merge($collection->images);
        }
        $board->images_counter = $allImages->count();

        return response()->json($board);
    }

    public function store(): JsonResponse
    {
        $board = Board::create([
            'name' => 'Untitled',
            'owner_id' => Auth::id()
        ]);
        return response()->json($board, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $boardId): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:60',
            'drag_collection_id' => 'nullable|integer|exists:collections,id',
            'drop_collection_id' => 'nullable|integer|exists:collections,id',
        ]);

        try {
            DB::beginTransaction();
            $board = Board::findOrFail($boardId);
            if (!empty($validated['name'])) {
                $board->forceFill(['name' => $validated['name']]);
            }

            if (!empty($validated['drag_collection_id']) && !empty($validated['drop_collection_id'])) {
                $dragCollection = $board->collections()->findOrFail($validated['drag_collection_id']);
                $dropCollection = $board->collections()->findOrFail($validated['drop_collection_id']);

                $tempOrder = $dragCollection->order;
                $dragCollection->order = $dropCollection->order;
                $dropCollection->order = $tempOrder;

                $dragCollection->save();
                $dropCollection->save();
            }

            $board->save();
            DB::commit();
            return response()->json($board);
        } catch (\Throwable $exception) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function createCollection(Request $request, int $boardId): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'order' => 'required|integer|min:1|max:5',
        ]);

        $board = Board::findOrFail($boardId);
        $collection = new Collection($validated);
        $collection->board_id = $board->id;
        $collection->save();

        return response()->json($board->load('collections'));
    }

    public function reorderCollection(Request $request, int $boardId, int $collectionId): JsonResponse
    {
        $board = Board::findOrFail($boardId);
        $collection = $board->collections()->findOrFail($collectionId);

        $request->validate([
            'name' => 'nullable|string|max:60',
            'order' => 'nullable|integer|min:1|max:5',
        ]);

        $collection
            ->forceFill($request->all())
            ->save();

        return response()->json($board->load('collections'));
    }

    public function deleteCollection(int $boardId, int $collectionId): JsonResponse
    {
        $board = Board::findOrFail($boardId);
        $collection = $board->collections()->findOrFail($collectionId);

        foreach ($collection->images as $image) {
            $image->update(['collection_id' => null]);
        }
        $collection->delete();

        $collections = $board->collections()->orderBy('order')->get();
        foreach ($collections as $index => $collection) {
            $collection->update(['order' => ++$index]);
        }

        return response()->json($board->load('collections'));
    }


    public function recentChanges(int $boardId): JsonResponse
    {
        $board = Board::findOrFail($boardId);
        $recentChanges = $board->recentChanges()
            ->with('user:name')
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json($recentChanges);
    }

}
