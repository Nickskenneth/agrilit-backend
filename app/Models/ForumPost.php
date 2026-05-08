<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'commodity',
        'image',
        'status',
        'is_answered',
        'views',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
    ];

    // =====================
    // SCOPES
    // =====================

    // Hanya post yang sudah dimoderasi/approved
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Post yang belum dijawab pakar (untuk dashboard pakar)
    public function scopeUnanswered($query)
    {
        return $query->where('is_answered', false)->where('status', 'approved');
    }

    public function scopeForCommodity($query, string $commodity)
    {
        return $query->where('commodity', $commodity);
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'post_id');
    }

    // Shortcut ke jawaban pakar saja
    public function expertReply()
    {
        return $this->hasOne(ForumReply::class, 'post_id')
            ->where('is_expert_answer', true);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        return asset('storage/' . $this->image);
    }

    // Hitung total balasan (tidak termasuk yang soft deleted)
    public function getReplyCountAttribute(): int
    {
        return $this->replies()->count();
    }
}
