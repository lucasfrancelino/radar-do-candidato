<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'board_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'contest_student')
                    ->withPivot('status', 'enrollment_date')
                    ->withTimestamps();
    }
}