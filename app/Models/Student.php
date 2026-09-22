<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'registration_number',
        'birth_date',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contests(): BelongsToMany
    {
        return $this->belongsToMany(Contest::class, 'contest_student')
                    ->withPivot('status', 'enrollment_date')
                    ->withTimestamps();
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'position_student')
                    ->withPivot('status', 'interest_date')
                    ->withTimestamps();
    }
}