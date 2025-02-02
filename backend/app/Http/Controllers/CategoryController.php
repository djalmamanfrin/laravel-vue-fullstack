<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
            ->select(['id', 'slug', 'name'])
            ->withCount(['images as images_count' => function ($query) {
                $query->where('user_id', Auth::id());
            }])
            ->get();

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
                'categories' => $image->categories->map(fn($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ]),
                'created_at' => $image->created_at,
                'created_at_formatted' => Carbon::parse($image->created_at)->format('M d, Y'),
            ];
        });

        return response()->json($images);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
