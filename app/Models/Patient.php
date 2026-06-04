<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'session_value',
        'package_sessions', 'sessions_done', 'notes', 'active', 'cpf', 'birth_date',
    ];

    protected $casts = [
        'session_value'    => 'decimal:2',
        'active'           => 'boolean',
        'birth_date'       => 'date',
        'package_sessions' => 'integer',
        'sessions_done'    => 'integer',
    ];

    // ─── Relacionamentos ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billingCycles()
    {
        return $this->hasMany(BillingCycle::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function pendingBalance(): float
    {
        return $this->billingCycles()
            ->whereIn('status', ['pending', 'late'])
            ->sum('amount');
    }

    public function whatsappLink(string $message = ''): string
    {
        $phone = preg_replace('/\D/', '', $this->phone ?? '');
        $text  = urlencode($message);
        return "https://wa.me/55{$phone}?text={$text}";
    }
}
