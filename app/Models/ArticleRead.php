<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'cached_at',
        'is_read',
    ];

    protected $casts = [
        'cached_at' => 'datetime',
        'is_read'   => 'boolean',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
