<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->with('images')
            ->select(['id', 'name'])
            ->get()
            ->makeHidden(['images']);

        return response()->json($categories);
    }

    public function images(int $categoryId): JsonResponse
    {
        $category = Category::findOrFail($categoryId);
        $images = $category->images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => url(Storage::url($image->path)),
                'description' => $image->description,
                'created_at' => $image->created_at,
                'created_at_formatted' => Carbon::parse($image->created_at)->format('M d, Y'),
            ];
        });
        return response()->json($images);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response(null, 204);
    }
}
