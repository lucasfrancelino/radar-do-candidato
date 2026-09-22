<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'name',
        'description',
        'vacancies',
        'salary',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'salary' => 'decimal:2', // Garante que o salário seja tratado como decimal com 2 casas
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'position_student')
                    ->withPivot('status', 'interest_date')
                    ->withTimestamps();
    }
}