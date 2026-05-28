<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail',
        'commodity',
        'category',
        'is_published',
        'published_at',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // =====================
    // BOOT — Auto-generate slug dari title
    // =====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            // Auto-isi excerpt dari 150 karakter pertama content
            if (empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 150);
            }
        });
    }

    // =====================
    // SCOPES — Shortcut query yang sering dipakai
    // =====================

    // Hanya artikel yang sudah dipublish
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Filter berdasarkan komoditas
    public function scopeForCommodity($query, string $commodity)
    {
        return $query->where('commodity', $commodity);
    }

    // Artikel terbaru yang diupdate (untuk sync delta di Flutter)
    public function scopeUpdatedAfter($query, $timestamp)
    {
        return $query->where('updated_at', '>', $timestamp);
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reads()
    {
        return $this->hasMany(ArticleRead::class);
    }

    // =====================
    // SCOUT — Meilisearch
    // =====================

    /**
     * Field yang di-index ke Meilisearch.
     * content_plain = HTML stripped → plain text agar keyword dalam isi artikel
     * bisa ditemukan tanpa noise dari tag HTML.
     */
    public function toSearchableArray(): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'excerpt'       => $this->excerpt ?? '',
            'content_plain' => strip_tags($this->content ?? ''),
            'category'      => $this->category,
            'commodity'     => $this->commodity,
            'is_published'  => $this->is_published,
        ];
    }

    /**
     * Hanya artikel yang sudah dipublish yang masuk ke index Meilisearch.
     * Artikel draft tidak akan pernah muncul di hasil pencarian.
     */
    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_published;
    }

    // =====================
    // ACCESSORS
    // =====================

    // URL thumbnail lengkap
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) return null;
        return asset('storage/' . $this->thumbnail);
    }
}
