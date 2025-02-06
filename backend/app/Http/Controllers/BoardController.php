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
        $boards = Board::query()
            ->where('owner_id', Auth::id())
            ->get();
        return response()->json($boards);
    }

    public function get(int $boardId): JsonResponse
    {
        $board = Board::with([
            'collections' => function ($query) {
                $query->orderBy('order');
            },
            'collections.images'
        ])->findOrFail($boardId);

        return response()->json($board);
    }

    public function update(Request $request, int $boardId): JsonResponse
    {
        $validated = $request->validate([
            'drag_collection_id' => 'nullable|integer|exists:collections,id',
            'drop_collection_id' => 'nullable|integer|exists:collections,id',
        ]);

        try {
            DB::beginTransaction();
            $board = Board::findOrFail($boardId);
            $dragCollection = $board->collections()->findOrFail($validated['drag_collection_id']);
            $dropCollection = $board->collections()->findOrFail($validated['drop_collection_id']);

            $tempOrder = $dragCollection->order;
            $dragCollection->order = $dropCollection->order;
            $dropCollection->order = $tempOrder;

            $dragCollection->save();
            $dropCollection->save();
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
        $request->validate([
            'order' => 'required|integer|min:1|max:5',
        ]);

        $board = Board::findOrFail($boardId);
        $collection = $board->collections()->findOrFail($collectionId);
        $collection->update(['order' => $request->order]);

        return response()->json($board->load('collections'));
    }

    public function deleteCollection(int $boardId, int $collectionId): JsonResponse
    {
        $board = Board::findOrFail($boardId);
        $collection = $board->collections()->findOrFail($collectionId);
        $collection->images()->update(['collection_id' => null]);
        $collection->delete();
        return response()->json($board->load('collections'));
    }

    public function recentChanges(int $boardId): JsonResponse
    {
        $board = Board::findOrFail($boardId);
        $recentChanges = $board->recentChanges()->get();
        return response()->json($recentChanges);
    }
}
