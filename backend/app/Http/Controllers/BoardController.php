<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        $board = Board::findOrFail($boardId);
        return response()->json($board);
    }
    public function recentChanges(int $boardId): JsonResponse
    {
        $board = Board::findOrFail($boardId);
        $recentChanges = $board->recentChanges()->get();
        return response()->json($recentChanges);
    }
}
