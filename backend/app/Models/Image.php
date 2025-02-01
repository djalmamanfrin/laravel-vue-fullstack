<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Image extends Model
{
    protected $fillable = ['path', 'slugs', 'description'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'image_category')
            ->where('user_id', Auth::id());
    }

    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'image_hashtag')
            ->where('user_id', Auth::id());
    }
}
