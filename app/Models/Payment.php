<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_profile_id',
        'recorded_by_user_id',
        'amount',
        'payment_date',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function memberProfile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function monthlyDues(): BelongsToMany
    {
        return $this->belongsToMany(MonthlyDues::class, 'payment_monthly_dues', 'payment_id', 'monthly_dues_id')
            ->withPivot('allocated_amount')
            ->withTimestamps();
    }
}
