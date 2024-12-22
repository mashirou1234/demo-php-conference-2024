<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cleaner extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'region'];

    public function schedules(): HasMany
    {
        return $this->hasMany(CleaningSchedule::class);
    }
}
