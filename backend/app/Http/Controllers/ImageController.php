<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Hashtag;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $images = Image::latest()
            ->get()
            ->map(function ($image) {
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

    public function get(int $imageId): JsonResponse
    {
        $image = Image::findOrFail($imageId);
        return response()->json([
            'id' => $image->id,
            'url' => url(Storage::url($image->path)),
            'description' => $image->description,
            'categories' => $image->categories
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg'],
        ]);

        $path = $request->file('image')->store('images', 'public');
        return response()->json(['path' => $path], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $imageId): JsonResponse
    {
        $request->validate([
            'path' => ['nullable', 'string'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();
            $image = Image::updateOrcreate(
                ['id' => $imageId],
                $request->all(),
            );
            logger()->info('before if description');
            if ($request->filled('description')) {
                logger()->info('inside if description');
                $image->fill(['patch' => $request->description]);
                preg_match_all('/#(\w+)/', $request->description, $matches);
                $hashtags = $matches[1] ?? [];

                $data = [];
                foreach ($hashtags as $value) {
                    $hashtag = Hashtag::firstOrCreate(
                        ['slug' => strtolower($value)],
                        ['value' => $value],
                    );
                    $data[] = ['hashtag_id' => $hashtag->id, 'user_id' => Auth::id()];
                }
                $image->hashtags()->sync($data);
            }

            $data = [];
            logger()->info('before if categories');
            logger()->info('categories: ' . json_encode($request->all()));
            if ($request->has('categories')) {
                logger()->info('inside if categories');
                foreach ($request->categories as $categoryName) {
                    $slug = Str::slug($categoryName, '_');

                    $category = Category::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $categoryName]
                    );
                    $data[] = ['category_id' => $category->id, 'user_id' => Auth::id()];
                }
                $image->categories()->sync($data);
            }
            DB::commit();
            return response()->json($image);
        } catch (\Throwable $exception) {
            DB::rollBack();
            if ($request->has('path')) {
                Storage::disk('public')->delete($request->path);
            }
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image): JsonResponse
    {
        try {
            DB::beginTransaction();
            $image->categories()->detach();
            $image->categories()->where('user_id', Auth::id())->delete();
            $image->hashtags()->detach();
            $image->hashtags()->where('user_id', Auth::id())->delete();
            $image->delete();
            DB::commit();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $exception) {
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
