<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    protected $fillable = ['slug', 'name'];

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'image_category')
            ->where('user_id', Auth::id());
    }
}
