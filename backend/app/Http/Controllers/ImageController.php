<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Image::latest()
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => url(Storage::url($image->path)),
                    'label' => $image->label,
                ];
            });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $path = $request->file('image')->store('images', 'public');

        $image = Image::create([
            'path' => $path,
            'description' => $request->description
        ]);

        if ($request->filled('description')) {
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

        if ($request->has('categories')) {
            $data = [];
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

        return response($image, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        $image->delete();

        return response(null, 204);
    }
}
