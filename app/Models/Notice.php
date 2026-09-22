<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'publication_date',
        'is_active',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'notice_subject')
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

    /**
     * Retorna a quantidade de questões já cadastradas para uma disciplina neste edital.
     */
    public function questionCountForSubject(Subject $subject): int
    {
        return $this->questions()
            ->where('subject_id', $subject->id)
            ->count();
    }

    /**
     * Verifica se a disciplina já atingiu o limite de 5 questões neste edital.
     */
    public function hasReachedMaxQuestionsForSubject(Subject $subject): bool
    {
        return $this->questionCountForSubject($subject) >= 5;
    }
}