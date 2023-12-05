<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function team(): BelongsTo
    {

        return $this->belongsTo(Team::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'task_label');
    }
}
