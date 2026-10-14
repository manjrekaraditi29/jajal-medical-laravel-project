<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalCase extends Model
{
    use HasFactory;

    protected $table = 'cases';

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_PLANNING = 'Planning';
    public const STATUS_IN_REVIEW = 'In Review';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PLANNING,
        self::STATUS_IN_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    public const PRIORITIES = ['Low', 'Medium', 'High'];

    protected $fillable = [
        'case_number',
        'patient_reference',
        'surgeon_name',
        'implant_type',
        'status',
        'priority',
        'surgery_date',
        'created_by',
    ];

    protected $casts = [
        'surgery_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('case_number', 'LIKE', "%{$term}%")
              ->orWhere('surgeon_name', 'LIKE', "%{$term}%")
              ->orWhere('patient_reference', 'LIKE', "%{$term}%");
        });
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status) || $status === 'All') return $query;
        return $query->where('status', $status);
    }
}