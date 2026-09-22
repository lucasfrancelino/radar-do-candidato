<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_id',
        'name',
        'description',
        'weight',
        'question_count',
        'elimination_criteria',
        'minimum_score',
        'historical_incidence',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'minimum_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}