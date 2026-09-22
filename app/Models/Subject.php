<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function notices(): BelongsToMany
    {
        return $this->belongsToMany(Notice::class, 'notice_subject')
                    ->withPivot([
                        'weight',
                        'question_count',
                        'elimination_criteria',
                        'minimum_score',
                        'historical_incidence',
                    ])
                    ->withTimestamps();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}