<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Hashtag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $images = Image::latest()->get();
        return response()->json($images);
    }

    public function get(int $imageId): JsonResponse
    {
        $image = Image::findOrFail($imageId);
        return response()->json($image);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg'],
        ]);

        $path = $request->file('image')->store('images', 'public');
        return response()->json(['title' => 'Not set', 'path' => $path], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $imageId): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string'],
            'collection_id' => ['nullable', 'integer',  'exists:collections,id'],
            'title' => ['nullable', 'string', 'max:60'],
        ]);

        try {
            DB::beginTransaction();
            $image = Image::updateOrcreate(
                ['id' => $imageId],
                $validated,
            );
            if ($request->filled('description')) {
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
