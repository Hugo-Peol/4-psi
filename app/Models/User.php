<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'crp', 'phone',
        'bio', 'city', 'state', 'approach', 'avatar', 'session_value',
        'is_admin', 'profile_public', 'whatsapp', 'instagram', 'specialty',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'profile_public'    => 'boolean',
        ];
    }

    // ─── Relacionamentos ─────────────────────────────────────────────────────

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function billingCycles()
    {
        return $this->hasMany(BillingCycle::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6C63FF&color=fff&size=128";
    }

    public function getProfileUrlAttribute(): string
    {
        return $this->username ? url('/' . $this->username) : '#';
    }

    // ─── Stats para o dashboard ──────────────────────────────────────────────

    public function monthlyRevenue(): float
    {
        return $this->billingCycles()
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
    }

    public function pendingAmount(): float
    {
        return $this->billingCycles()
            ->whereIn('status', ['pending', 'late'])
            ->sum('amount');
    }

    public function overdueCount(): int
    {
        return $this->billingCycles()
            ->where('status', 'late')
            ->count();
    }
}
