<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Hashtag extends Model
{
    protected $fillable = ['slug', 'value'];

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'image_hashtag')
            ->where('user_id', Auth::id());
    }
}
