<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyDues extends Model
{
    use HasFactory;

    protected $table = 'monthly_dues';

    protected $fillable = [
        'member_profile_id',
        'month',
        'year',
        'required_amount',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'required_amount' => 'decimal:2',
        ];
    }

    public function memberProfile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class);
    }
}