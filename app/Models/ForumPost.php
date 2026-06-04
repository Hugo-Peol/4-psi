<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'body', 'category', 'anonymous',
        'anonymous_label', 'pinned', 'views', 'replies_count',
    ];

    protected $casts = [
        'anonymous' => 'boolean',
        'pinned'    => 'boolean',
        'views'     => 'integer',
        'replies_count' => 'integer',
    ];

    // ─── Boot — anonimização automática ──────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (ForumPost $post) {
            if ($post->anonymous) {
                $number = rand(10, 999);
                $post->anonymous_label = "Psicólogo Anônimo #{$number}";
            }
        });
    }

    // ─── Relacionamentos ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopePinned($query)
    {
        return $query->where('pinned', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getAuthorNameAttribute(): string
    {
        return $this->anonymous
            ? ($this->anonymous_label ?? 'Psicólogo Anônimo')
            : $this->user->name;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'casos_clinicos' => 'Casos Clínicos',
            'materiais'      => 'Materiais',
            'indicacoes'     => 'Indicações',
            'duvidas'        => 'Dúvidas',
            default          => 'Geral',
        };
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
