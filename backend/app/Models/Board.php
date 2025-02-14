<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'owner_id',
        'board_id',
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['created_at_formatted'];

    public function getCreatedAtFormattedAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('M d, Y');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'board_members')
            ->withPivot('status_id')
            ->withTimestamps()
            ->withTrashed();
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function recentChanges(): HasMany
    {
        return $this->hasMany(RecentChange::class);
    }
}

