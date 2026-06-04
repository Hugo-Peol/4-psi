<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'patient_id', 'billing_cycle_id', 'cid_code',
        'session_date', 'amount', 'session_count', 'health_plan',
        'observations', 'pdf_path', 'receipt_number',
    ];

    protected $casts = [
        'session_date'  => 'date',
        'amount'        => 'decimal:2',
        'session_count' => 'integer',
    ];

    // ─── Boot — número sequencial automático ─────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Receipt $receipt) {
            $last  = static::where('user_id', $receipt->user_id)->max('id') ?? 0;
            $year  = now()->year;
            $seq   = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            $receipt->receipt_number = "REC-{$year}-{$seq}";
        });
    }

    // ─── Relacionamentos ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function billingCycle()
    {
        return $this->belongsTo(BillingCycle::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
