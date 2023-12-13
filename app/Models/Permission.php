<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
