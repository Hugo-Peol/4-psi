<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'forum_post_id', 'user_id', 'body', 'anonymous', 'anonymous_label',
    ];

    protected $casts = [
        'anonymous' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ForumReply $reply) {
            if ($reply->anonymous) {
                $number = rand(10, 999);
                $reply->anonymous_label = "Psicólogo Anônimo #{$number}";
            }
        });

        static::created(function (ForumReply $reply) {
            $reply->forumPost()->increment('replies_count');
        });

        static::deleted(function (ForumReply $reply) {
            $reply->forumPost()->decrement('replies_count');
        });
    }

    public function forumPost()
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->anonymous
            ? ($this->anonymous_label ?? 'Psicólogo Anônimo')
            : $this->user->name;
    }
}
