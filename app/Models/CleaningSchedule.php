<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleaningSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['cleaner_id', 'property_id', 'scheduled_date', 'standby_cleaner_id'];

    public function cleaner(): BelongsTo
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
